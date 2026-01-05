<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ExerciseCompletion;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ExerciseController extends Controller
{
    // GET

    public function index(Request $request)
    {
        $exercises = Exercise::select("id", "lecture_id", "block")->with('lecture:id,title')->get();

        // Group exercises by lecture id
        $grouped = $exercises->groupBy(fn($exercise) => $exercise->lecture->id);

        // Transform into a collection of lectures with exercises
        $lectures = $grouped->map(function ($exercises) {
            $lecture = $exercises->first()->lecture;

            // Map exercises to include header from block
            $exercisesWithHeader = $exercises->map(function ($exercise) {
                $header = $exercise->block->data->header;
                $exercise->header = $header;

                return $exercise;
            });

            return (object)[
                "id" => $lecture->id,
                "title" => $lecture->title,
                "exercises" => $exercisesWithHeader
            ];
        })->values();

        return view("exercises.index", [
            "hideSidebar" => true,
            "lectures" => $lectures
        ]);
    }

    public function show(Request $request, Exercise $exercise)
    {
        $user = $request->user();

        if ($user) {
            if ($user->completedExercises->contains("id", $exercise->id)) {
                $exercise->block["data"]["code"] = $exercise->code;
            }
        }

        $completedExercises = $user?->completedExercises->pluck("exercise_id") ?? collect();

        return view("exercises.show", [
            "hideSidebar" => true,
            "completedExercises" => $completedExercises,
            "block" => $exercise->block,
        ]);
    }

    // POST

    public function submitSolution(Request $request, Exercise $exercise)
    {
        $data = $request->validate([
            "code" => "required"
        ]);

        $user = $request->user();

        // Parse tests, create test cases & checks
        $tests = json_decode($exercise->tests, true);
        $checks = [];
        $testCases = [];
        $inputs = []; // TODO: add mocked inputs support later

        foreach ($tests as $test) {
            switch ($test["type"]) {
                case "variable":
                    $checks[] = [
                        "type" => "variable",
                        "name" => $test["variable"]
                    ];
                    $testCases[] = [
                        "type" => "variable",
                        "name" => $test["variable"],
                        "expected" => $test["raw"]
                    ];
                    break;

                case "function":
                    $checks[] = [
                        "type" => "function",
                        "name" => $test["name"],
                        "arg_count" => count($test["args"])
                    ];
                    $testCases[] = [
                        "type" => "function",
                        "name" => $test["name"],
                        "args" => $test["args"],
                        "expected" => $test["expected"]
                    ];
                    break;

                case "print":
                    $testCases[] = [
                        "type" => "print",
                        "expected" => trim($test["expected"], "\"")
                    ];
                    break;

                case "type":
                    $checks[] = [
                        "type" => "type",
                        "name" => $test["name"],
                        "expected" => $test["expected"]
                    ];
                    $testCases[] = [
                        "type" => "type",
                        "name" => $test["name"],
                        "expected" => $test["expected"]
                    ];
                    break;
            }
        }

        // Temp file
        $tempFile = storage_path("app/exercise_" . uniqid() . ".py");
        file_put_contents($tempFile, $request->code);

        // Prepare environment variables
        $checksJson = json_encode($checks);
        $testsJson = json_encode($testCases);
        $inputsJson = implode("\n", array_map(fn($v) => (string)$v, $inputs));

        // Run inside docker
        $command = [
            "docker",
            "run",
            "--rm",
            "--network=none",
            "--cpus=0.5",
            "--memory=100m",
            "--tmpfs",
            "/code:rw,size=15m",
            "-e",
            "PYTHON_CHECKS=$checksJson",
            "-e",
            "PYTHON_TESTS=$testsJson",
            "-e",
            "PYTHON_INPUTS=$inputsJson",
            "-v",
            "$tempFile:/code/main.py:ro",
            "-w",
            "/code",
            "fernefer/python-3.12-slim-student-exercise:1.0",
            "python3",
            "/code/main.py"
        ];

        $process = new Process($command, cwd: base_path(), timeout: 10);
        $process->run();

        // Delete temp file
        unlink($tempFile);

        // Error message for checks
        $error = mb_convert_encoding($process->getErrorOutput(), "UTF-8", "auto");
        // The output (in lines) of checks & tests
        $outputLines = explode("\n", trim($process->getOutput()));

        // Get user output
        $userOutput = "";
        $outputStart = array_search("USER OUTPUT", $outputLines);
        $outputEnd = array_search("USER OUTPUT END", $outputLines);
        $userOutput = implode("\n", array_slice($outputLines, $outputStart + 1, $outputEnd - $outputStart - 1));
        $userOutput = trim(mb_convert_encoding($userOutput, "UTF-8", "auto"));

        if ($error) {
            return response()->json([
                "test_results" => [],
                "error" => $error,
                "user_output" => $userOutput
            ]);
        }

        // Go through all checks
        // $checksStart = array_search("CHECKS", $outputLines);
        // $checksEnd = array_search("TESTS", $outputLines);
        // foreach (array_slice($outputLines, $checksStart + 1, $checksEnd - $checksStart - 1) as $i => $line) {
        //     if ($line == "False") {
        //         $check = $checks[$i];

        //         switch ($check["type"]) {
        //             case "variable":
        //                 $error = "Premenná {$check["name"]} nie je definovaná.";
        //                 break;

        //             case "function":
        //                 $error = "Funkcia {$check["name"]} nie je definovaná alebo je nesprávne definovaná.";
        //                 break;

        //             case "type":
        //                 $error = "Premenná {$check["name"]} má nesprávny dátový typ: očakávali sme {$check["expected"]}.";
        //                 break;
        //         }

        //         break;
        //     }
        // }

        // Go through all tests & keep track of the test results
        $testResults = collect();

        $testsStart = array_search("TESTS", $outputLines);
        $testsEnd = array_search("TESTS END", $outputLines);
        foreach (array_slice($outputLines, $testsStart + 1, $testsEnd - $testsStart - 1) as $i => $line) {
            $testCase = $testCases[$i];
            $success = $line == "True";

            if (!$success) {
                switch ($testCase["type"]) {
                    case "variable":
                        $message = "Premenná <b>{$testCase["name"]}</b> nemá správnu hodnotu.";
                        break;

                    case "function":
                        $message = "Funkcia <b>{$testCase["name"]}</b> nevracia správnu hodnotu.";
                        break;

                    case "print":
                        $message = "<b>\"{$testCase["expected"]}\"</b> nebolo vypísané do konzole.";
                        break;

                    case "type":
                        $message = "Premenná <b>{$testCase["name"]}</b> nemá správny dátový typ.";
                        break;
                }
            } else {
                switch ($testCase["type"]) {
                    case "variable":
                        $message = "Premenná <b>{$testCase["name"]}</b> má správnu hodnotu.";
                        break;

                    case "function":
                        $message = "Funkcia <b>{$testCase["name"]}</b> vracia správnu hodnotu.";
                        break;

                    case "print":
                        $message = "<b>\"{$testCase["expected"]}\"</b> bolo vypísané do konzole.";
                        break;

                    case "type":
                        $message = "Premenná <b>{$testCase["name"]}</b> má správny dátový typ.";
                        break;
                }
            }

            $testResults->push([
                "success" => $success,
                "message" => $message
            ]);
        }

        // If this exercise was already completed and everything is okay, we resave the users submitted code
        $allTestsPassed = $testResults->every(fn($testResult) => $testResult["success"]);
        $successfulCompletion = !$error && $allTestsPassed;
        if ($successfulCompletion && $user->completedExercises->contains("exercise_id", $exercise->id)) {
            ExerciseCompletion::where("user_id", $user->id)->where("exercise_id", $exercise->id)->update([
                "code" => $request->code
            ]);
        }

        return response()->json([
            "test_results" => $testResults,
            "error" => $error,
            "user_output" => $userOutput,
            ...($successfulCompletion ? ["celebrate" => "Skvele! Cvičenie si úspešne zvládol, len tak ďalej!"] : [])
        ]);
    }
}
