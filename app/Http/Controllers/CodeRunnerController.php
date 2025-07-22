<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Symfony\Component\Process\Process;

class CodeRunnerController extends Controller
{
    public function runCode(Request $request)
    {
        $request->validate([
            "code" => "required"
        ]);

        // Temp file
        $tempFile = storage_path("app/code_runner_" . uniqid() . ".py");
        file_put_contents($tempFile, $request->code);

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

        $output = $process->isSuccessful() ? $process->getOutput() : $process->getErrorOutput();
        $output = mb_convert_encoding($output, "UTF-8", "auto");

        return response()->json([
            "success" => $process->isSuccessful(),
            "output" => $output
        ]);
    }
}
