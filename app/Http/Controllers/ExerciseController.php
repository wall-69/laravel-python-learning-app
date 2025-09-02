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
                $block = json_decode($exercise->block, true);
                $header = $block["data"]["header"];
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
            "block" => json_decode($exercise->block, true),
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
        $checks = collect();
        $testCases = [];

        foreach ($tests as $test) {
            switch ($test["type"]) {
                case "variable":
                    $testCases[] = [
                        "type" => "variable",
                        "name" => $test["variable"],
                        "test" => "print({$test["raw"]})"
                    ];
                    $checks->push(["type" => "variable", "name" => $test["variable"]]);
                    break;

                case "function":
                    $args = implode(", ", $test["args"]);

                    $testCases[] = [
                        "type" => "function",
                        "name" => $test["name"],
                        "args" => $test["args"],
                        "expected" => $test["expected"],
                        "test" => "print({$test["name"]}({$args}) == {$test["expected"]})",
                    ];
                    $checks->push(["type" => "function", "name" => $test["name"], "args" => $test["args"]]);
                    break;

                case "print":
                    $testCases[] = [
                        "type" => "print",
                        "expected" => $test["expected"],
                        "test" => "print({$test["expected"]} in __user_output)"
                    ];
                    break;

                case "type":
                    $checks->push([
                        "type" => "type",
                        "name" => $test["name"],
                        "expected" => $test["expected"]
                    ]);
                    break;
            }
        }

        // Put type checks at the end (so variable checks are first)
        $checks = collect($checks)
            ->sortBy(function ($check) {
                return $check["type"] === "type";
            })
            ->values()
            ->toArray();

        // Temp file
        $tempFile = storage_path("app/exercise_" . uniqid() . ".py");

        // Markers
        $CHECKS_MARKER = "CHECKS";
        $TESTS_MARKER = "TESTS";
        $TESTS_END_MARKER = "TESTS END";

        // Take over stdout
        $code = <<<TAKEOVER
        import sys
        from io import StringIO
        
        __old_stdout = sys.stdout
        __buffer = StringIO()
        sys.stdout = __buffer\n
        TAKEOVER;

        $code .= $request->code . "\n";

        // Give back stdout
        $code .= <<<GIVE
        sys.stdout = __old_stdout
        __user_output = __buffer.getvalue().splitlines()\n
        GIVE;

        // Checks
        $code .= "print('$CHECKS_MARKER')\n";
        $code .= "import inspect\n";

        foreach ($checks as $check) {
            switch ($check["type"]) {
                case "variable":
                    $code .= "print('{$check["name"]}' in locals())" . "\n";
                    break;

                case "function":
                    $argCount = count($check['args']);

                    $code .= "print('{$check["name"]}' in locals()"
                        . " and callable(locals()['{$check["name"]}'])"
                        . " and len(inspect.signature(locals()['{$check["name"]}']).parameters) == $argCount)"
                        . "\n";
                    break;

                case "type":
                    $code .= "print('{$check["name"]}' in locals() and type({$check["name"]}) is {$check["expected"]})\n";
                    break;
            }
        }

        // Tests
        $code .= "print('$TESTS_MARKER')\n";

        foreach ($testCases as $testCase) {
            $code .= $testCase["test"] . "\n";
        }

        $code .= "print('$TESTS_END_MARKER')\n";

        // Put the users code & tests into the temp file
        file_put_contents($tempFile, $code);

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
            "-v",
            "$tempFile:/code/main.py:ro",
            "-w",
            "/code",
            "python:3.12-slim",
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
        $outputLines = explode("\n", $process->getOutput());

        // Go through all checks
        $checksStart = array_search($CHECKS_MARKER, $outputLines);
        $checksEnd = array_search($TESTS_MARKER, $outputLines);
        foreach (array_slice($outputLines, $checksStart + 1, $checksEnd - $checksStart - 1) as $i => $line) {
            if ($line == "False") {
                $check = $checks[$i];

                switch ($check["type"]) {
                    case "variable":
                        $error = "Premenná {$check["name"]} nie je definovaná.";
                        break;

                    case "function":
                        $error = "Funkcia {$check["name"]} nie je definovaná alebo je nesprávne definovaná.";
                        break;

                    case "type":
                        $error = "Premenná {$check["name"]} má nesprávny dátový typ: očakávali sme {$check["expected"]}.";
                        break;
                }

                break;
            }
        }

        // Go through all tests & keep track of the test results
        $testResults = collect();

        $testsStart = array_search($TESTS_MARKER, $outputLines);
        $testsEnd = array_search($TESTS_END_MARKER, $outputLines);
        foreach (array_slice($outputLines, $testsStart + 1, $testsEnd - $testsStart - 1) as $i => $line) {
            $testCase = $testCases[$i];

            if ($line == "False") {
                switch ($testCase["type"]) {
                    case "variable":
                        $message = "Premenná <b>{$testCase["name"]}</b> nie je správna.";
                        break;

                    case "function":
                        $message = "Funkcia <b>{$testCase["name"]}</b> nevracia správnu hodnotu.";
                        break;

                    case "print":
                        $message = "<b>{$testCase["expected"]}</b> nebolo vypísané do konzole.";
                        break;
                }

                $success = false;
            } else {
                switch ($testCase["type"]) {
                    case "variable":
                        $message = "Premenná <b>{$testCase["name"]}</b> je správna.";
                        break;

                    case "function":
                        $message = "Funkcia <b>{$testCase["name"]}</b> vracia správnu hodnotu.";
                        break;

                    case "print":
                        $message = "<b>{$testCase["expected"]}</b> bolo vypísané do konzole.";
                        break;
                }

                $success = true;
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
            ...($successfulCompletion ? ["celebrate" => "Skvele! Cvičenie si úspešne zvládol, len tak ďalej!"] : [])
        ]);
    }
}
