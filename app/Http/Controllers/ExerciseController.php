<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class ExerciseController extends Controller
{
    // POST

    public function submitSolution(Request $request, Exercise $exercise)
    {
        $data = $request->validate([
            "code" => "required"
        ]);

        // Parse tests
        $tests = json_decode($exercise->tests, true);
        $checks = [];
        $testCases = [];
        foreach ($tests as $test) {
            $testType = $test["type"];
            switch ($testType) {
                case "variable":
                    $testCases[] = [
                        "type" => "variable",
                        "name" => $test["variable"],
                        "test" => "print({$test["raw"]})"
                    ];
                    $checks[] = ["type" => "variable", "name" => $test["variable"]];
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
                    $checks[] = ["type" => "function", "name" => $test["name"], "args" => $test["args"]];
                    break;
                case "print":
                    $testCases[] = [
                        "type" => "print",
                        "expected" => $test["expected"],
                        "test" => "print({$test["expected"]} in __user_output)"
                    ];
                    break;

                case "type":
                    $checks[] = [
                        "type" => "type",
                        "name" => $test["name"],
                        "expected" => $test["expected"]
                    ];
                    break;
            }
        }

        // Temp file
        $tempFile = storage_path("app/exercise_" . uniqid() . ".py");


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

        $code .= "print('CHECKS')\n";
        $code .= "import inspect\n";

        foreach ($checks as $check) {
            if ($check["type"] == "variable") {
                $code .= "print('{$check["name"]}' in locals())" . "\n";
            } else if ($check["type"] == "function") {
                $argCount = count($check['args']);

                $code .= "print('{$check["name"]}' in locals()"
                    . " and callable(locals()['{$check["name"]}'])"
                    . " and len(inspect.signature(locals()['{$check["name"]}']).parameters) == $argCount)"
                    . "\n";
            } else if ($check["type"] == "type") {
                $code .= "print(type({$check["name"]}) is {$check["expected"]})\n";
            }
        }

        $code .= "print('TESTS')\n";

        foreach ($testCases as $testCase) {
            $code .= $testCase["test"] . "\n";
        }

        $code .= "print('TESTS END')\n";

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

        $error = mb_convert_encoding($process->getErrorOutput(), "UTF-8", "auto");

        $outputLines = explode("\n", $process->getOutput());
        $checksStart = array_search("CHECKS", $outputLines);
        $checksEnd = array_search("TESTS", $outputLines);
        foreach (array_slice($outputLines, $checksStart + 1, $checksEnd - $checksStart - 1) as $i => $line) {
            if ($line == "False") {
                $check = $checks[$i];

                if ($check["type"] == "variable") {
                    $error = "Premenná {$check["name"]} nie je definovaná.";
                } else if ($check["type"] == "function") {
                    $error = "Funkcia {$check["name"]} nie je definovaná alebo je nesprávne definovaná.";
                } else if ($check["type"] == "type") {
                    $error = "Premenná {$check["name"]} má nesprávny dátový typ: očakávali sme {$check["expected"]}.";
                }
            }
        }

        $testResults = [];
        $testsStart = array_search("TESTS", $outputLines);
        $testsEnd = array_search("TESTS END", $outputLines);
        foreach (array_slice($outputLines, $testsStart + 1, $testsEnd - $testsStart - 1) as $i => $line) {
            $testCase = $testCases[$i];
            $message = "";

            if ($line == "False") {
                if ($testCase["type"] == "variable") {
                    $message = "Premenná <b>{$testCase["name"]}</b> nie je správna.";
                } else if ($testCase["type"] == "function") {
                    $message = "Funkcia <b>{$testCase["name"]}</b> nevracia správnu hodnotu.";
                } else if ($testCase["type"] == "print") {
                    $message = "<b>{$testCase["expected"]}</b> nebolo vypísané do konzole.";
                }

                $testResults[] = [
                    "success" => false,
                    "message" => $message
                ];
            } else {
                if ($testCase["type"] == "variable") {
                    $message = "Premenná <b>{$testCase["name"]}</b> je správna.";
                } else if ($testCase["type"] == "function") {
                    $message = "Funkcia <b>{$testCase["name"]}</b> vracia správnu hodnotu.";
                } else if ($testCase["type"] == "print") {
                    $message = "<b>{$testCase["expected"]}</b> bolo vypísané do konzole.";
                }

                $testResults[] = [
                    "success" => true,
                    "message" => $message
                ];
            }
        }

        return response()->json([
            "test_results" => $testResults,
            "error" => $error
        ]);
    }
}
