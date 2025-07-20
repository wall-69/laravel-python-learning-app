<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class CodeRunnerController extends Controller
{
    public function runCode(Request $request)
    {
        $request->validate([
            "code" => "required"
        ]);

        $userCode = $request->code;

        // TODO: REMAKE WITH DOCKER

        // Write to a temp file
        $tempFile = storage_path('app/code_runner_' . uniqid() . '.py');
        file_put_contents($tempFile, $userCode);

        // Run it
        $env = $_SERVER;
        if (!isset($env['SystemRoot'])) {
            $env['SystemRoot'] = 'C:\\Windows';
        }
        $env['PYTHONIOENCODING'] = 'utf-8';

        $process = new Process(
            ['C:\\Program Files\\Python310\\python.exe', $tempFile],
            null,
            $env
        );
        $process->run();

        // Optional: delete temp file
        unlink($tempFile);

        $output = $process->isSuccessful() ? $process->getOutput() : $process->getErrorOutput();

        $output = mb_convert_encoding($output, 'UTF-8', 'auto');

        return response()->json([
            "output" => $output
        ]);
    }
}
