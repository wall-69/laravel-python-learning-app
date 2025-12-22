const http = require("http").createServer();
const io = require("socket.io")(http, { cors: { origin: "*" } });
const Docker = require("dockerode");
const docker = new Docker();
const { log, warn, error } = require("./helpers.js");

// Timeout
const TIMEOUT_SECONDS = 7;

// Queue
const MAX_CONCURRENT = 10;
let currentlyRunning = 0;
const queue = [];

// Signals
const INPUT_SIGNAL = "\x02\x05\x03";

async function executeCode(socket, data) {
    let container = null;
    let timeoutHandle = null;

    try {
        const codeInjectionLines = [
            "import builtins, sys",
            "_raw_input = builtins.input",
            'def magic_input(prompt=""):',
            `    sys.stdout.write("${INPUT_SIGNAL}")`,
            "    sys.stdout.flush()",
            "    return _raw_input(prompt)",
            "builtins.input = magic_input",
        ];
        const LINE_OFFSET = codeInjectionLines.length;
        const codeInjection = codeInjectionLines.join("\n");

        const wrappedCode = `${codeInjection}\n${data.code}`.trim();

        container = await docker.createContainer({
            Image: "python:3.12-slim",
            Cmd: ["python3", "-u", "-c", wrappedCode],
            AttachStdin: true,
            AttachStdout: true,
            AttachStderr: true,
            Tty: true,
            OpenStdin: true,
            StdinOnce: false,
            HostConfig: {
                Memory: 64 * 1024 * 1024,
                CpuQuota: 30000,
                NetworkMode: "none",
                AutoRemove: false,
            },
        });

        const stream = await container.attach({
            stream: true,
            stdin: true,
            stdout: true,
            stderr: true,
            hijack: true,
        });

        let isFirstChunk = true;
        stream.on("data", (chunk) => {
            let output = chunk.toString();

            if (isFirstChunk) {
                // This removes the "{"stream":true..." JSON if it exists
                // and any null bytes/headers Docker TTY sends
                output = output.replace(/^.*\{.*"hijack":true\}/s, "");
                isFirstChunk = false;
            }

            // Handle input signal
            if (output.includes(INPUT_SIGNAL)) {
                if (timeoutHandle) clearTimeout(timeoutHandle);

                socket.emit("waiting_for_input");
                output = output.split(INPUT_SIGNAL).join("");
            }

            // Fix line numbers in error messages
            output = fixLineNumbers(output, LINE_OFFSET);

            if (output.length > 0) {
                socket.emit("output", output);
            }
        });

        socket.on("stdin", (input) => {
            if (stream && stream.writable) {
                stream.write(input);
            }
        });

        await container.start();

        const waitPromise = container.wait();
        const timeoutPromise = new Promise((_, reject) => {
            timeoutHandle = setTimeout(() => {
                reject(new Error("Execution timeout"));
            }, TIMEOUT_SECONDS * 1000);
        });

        await Promise.race([waitPromise, timeoutPromise]);
        clearTimeout(timeoutHandle);

        await container.remove({ force: true }).catch(() => {});

        socket.disconnect();
    } catch (err) {
        if (timeoutHandle) clearTimeout(timeoutHandle);

        socket.emit("error", fixLineNumbers(err.message, LINE_OFFSET));

        // Remove container if it exists (ignore any removal related errors)
        if (container) {
            await container.remove({ force: true }).catch(() => {});
        }

        socket.disconnect();
    } finally {
        currentlyRunning--;
        processQueue();
    }
}

function fixLineNumbers(text, lineOffset) {
    // Replace "<string>" with "main.py" and adjust line numbers
    return text
        .replace(/<string>/g, "main.py")
        .replace(/File "main\.py", line (\d+)/g, (match, lineNum) => {
            const originalLine = parseInt(lineNum);
            const adjustedLine = Math.max(1, originalLine - lineOffset);
            return `File "main.py", line ${adjustedLine}`;
        });
}

function processQueue() {
    while (queue.length > 0 && currentlyRunning < MAX_CONCURRENT) {
        const { socket, data } = queue.shift();
        currentlyRunning++;
        executeCode(socket, data);
    }
}

io.on("connection", (socket) => {
    log("User connected");

    socket.on("run", async (data) => {
        if (currentlyRunning >= MAX_CONCURRENT) {
            queue.push({ socket, data });

            log(`Queue full, adding to queue (${queue.length} waiting)`);
        } else {
            currentlyRunning++;
            executeCode(socket, data);
        }
    });

    socket.on("error", (err) => {
        error("(Socket Error) " + err);
    });

    socket.on("disconnect", () => {
        log("User disconnected");
        // Remove from queue if still waiting
        const index = queue.findIndex((item) => item.socket === socket);
        if (index !== -1) {
            queue.splice(index, 1);
        }
    });
});

http.listen(3000, () => log("Code Runner active on 3000"));
