<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class CodeRunnerController extends Controller
{
    public function runCode(Request $request)
    {
        $data = $request->validate([
            "code" => "required"
        ]);

        $userCode = $data["code"];

        // Write to a temp file
        $tempFile = storage_path('app/code_runner_' . uniqid() . '.py');
        file_put_contents($tempFile, $userCode);

        // Run it
        $env = $_SERVER;
        if (!isset($env['SystemRoot'])) {
            $env['SystemRoot'] = 'C:\\Windows';
        }
        $process = new Process(
            ['C:\\Program Files\\Python310\\python.exe', $tempFile],
            null,
            $env
        );
        $process->run();

        // Optional: delete temp file
        unlink($tempFile);

        $output = "";

        // Handle result
        if (!$process->isSuccessful()) {
            $output = $process->getErrorOutput();
        } else {
            $output = $process->getOutput();
        }

        return response()->json([
            "output" => $output
        ]);
    }
}
