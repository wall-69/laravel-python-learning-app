const http = require("http").createServer();
const io = require("socket.io")(http, { cors: { origin: "*" } });
const Docker = require("dockerode");
const docker = new Docker();
const { log, warn, error } = require("./helpers.js");

// Memory
const MEMORY_LIMIT_ERROR_MESSAGE =
    "Terminated: Memory limit exceeded - your program used too much memory.";

// Timeout
const TIMEOUT_SECONDS = 5;
const TIMEOUT_ERROR_MESSAGE =
    "Terminated: Timeout - your program took too long to run.";

// Max output
const MAX_OUTPUT_SIZE = 512 * 1024;
const MAX_OUTPUT_SIZE_ERROR_MESSAGE = "Terminated: Max output size exceeded.";

// Queue
const MAX_CONCURRENT = 10;
let currentlyRunning = 0;
const queue = [];

// Signals
const INPUT_SIGNAL = "\x02\x05\x03";

async function executeCode(socket, data) {
    let container = null;
    let finished = false;

    let timeoutHandle = null;
    let totalOutputSize = 0;

    socket.on("disconnect", () => cleanup("User disconnected"));

    async function cleanup(reason) {
        if (finished) return;
        finished = true;

        if (timeoutHandle) {
            clearTimeout(timeoutHandle);
            timeoutHandle = null;
        }

        if (container) {
            try {
                // Check state before killing
                const state = await container.inspect().catch(() => null);
                if (state && state.State.Running) {
                    await container.kill().catch(() => {});
                }

                // Try to remove if its not already gone
                await container.remove({ force: true }).catch(() => {});
            } catch (err) {
                error("Cleanup error: " + err.message);
            }
        }

        if (socket.connected) {
            if (reason) socket.emit("error", reason);
            socket.disconnect();
        }

        currentlyRunning--;
        processQueue();
    }

    try {
        container = await docker.createContainer({
            Image: "fernefer/python-3.12-slim-student:1.0",
            Cmd: [
                "sh",
                "-c",
                `cat << 'EOF' > /main.py && python3 -u /main.py\n${data.code}\nEOF`,
            ],
            AttachStdin: true,
            AttachStdout: true,
            AttachStderr: true,
            Tty: true,
            OpenStdin: true,
            StdinOnce: false,
            HostConfig: {
                Memory: 64 * 1024 * 1024,
                CpuQuota: 50000,
                NetworkMode: "none",
                AutoRemove: true,
            },
        });

        const stream = await container.attach({
            stream: true,
            stdin: true,
            stdout: true,
            stderr: true,
            hijack: true,
        });

        stream.on("data", (chunk) => {
            totalOutputSize += chunk.length;

            if (totalOutputSize > MAX_OUTPUT_SIZE) {
                cleanup(MAX_OUTPUT_SIZE_ERROR_MESSAGE);
                stream.destroy();
                return;
            }

            let output = chunk
                .toString()
                .replace(/^.*\{.*"hijack":true\}/s, "");

            if (output.includes(INPUT_SIGNAL)) {
                // reset/rearm timeout to TIMEOUT_SECONDS whenever program waits for input
                if (timeoutHandle) {
                    clearTimeout(timeoutHandle);
                    timeoutHandle = null;
                }
                // arm a new timeout for the input period
                timeoutHandle = setTimeout(
                    () => cleanup(TIMEOUT_ERROR_MESSAGE),
                    TIMEOUT_SECONDS * 1000
                );

                socket.emit("waiting_for_input");
                output = output.split(INPUT_SIGNAL).join("");
            }

            if (output.length > 0) {
                socket.emit("output", output);
            }
        });

        socket.on("input", (input) => {
            if (stream && stream.writable) {
                stream.write(input);
            }

            // User provided input — reset input timeout so next input has full TIMEOUT_SECONDS
            if (timeoutHandle) {
                clearTimeout(timeoutHandle);
                timeoutHandle = setTimeout(
                    () => cleanup(TIMEOUT_ERROR_MESSAGE),
                    TIMEOUT_SECONDS * 1000
                );
            }
        });

        await container.start();

        // Start a timeout that will cleanup if the whole run takes too long
        timeoutHandle = setTimeout(
            () => cleanup(TIMEOUT_ERROR_MESSAGE),
            TIMEOUT_SECONDS * 1000
        );

        // Wait for container to finish. If timeout triggers, cleanup() will kill the container
        await container.wait();

        // If we reached here, the container finished; clear any remaining timeout
        if (timeoutHandle) {
            clearTimeout(timeoutHandle);
            timeoutHandle = null;
        }

        let inspect;
        try {
            inspect = await container.inspect();
        } catch (err) {
            // Container was likely auto-removed, cannot inspect
            inspect = null;
        }

        if (inspect && inspect.State.OOMKilled) {
            await cleanup(MEMORY_LIMIT_ERROR_MESSAGE);
            return;
        }

        await cleanup();
    } catch (err) {
        await cleanup(err.message);
    }
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
