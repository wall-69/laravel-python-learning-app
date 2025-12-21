const http = require("http").createServer();
const io = require("socket.io")(http, { cors: { origin: "*" } });
const Docker = require("dockerode");
const docker = new Docker();
const { log, warn, error } = require("./helpers.js");

const TIMEOUT_SECONDS = 7;
const MAX_CONCURRENT = 10;
let currentlyRunning = 0;
const queue = [];

const READY_SIGNAL = "\x02PYTHON_READY\x03";
const INPUT_SIGNAL = "\x02WAITING_FOR_INPUT\x03";

async function executeCode(socket, data) {
    let container = null;
    let timeoutHandle = null;

    try {
        const wrappedCode = `
import builtins, sys
_raw_input = builtins.input
def magic_input(prompt=""):
    sys.stdout.write("${INPUT_SIGNAL}")
    sys.stdout.flush()
    return _raw_input(prompt)
builtins.input = magic_input

# THE CLEAN START SIGNAL
sys.stdout.write("${READY_SIGNAL}")
sys.stdout.flush()

${data.code}
`.trim();

        container = await docker.createContainer({
            Image: "python:3.12-slim",
            Cmd: ["python3", "-u", "-c", wrappedCode],
            AttachStdin: true,
            AttachStdout: true,
            AttachStderr: true,
            Tty: true, // <--- TURN THIS ON
            OpenStdin: true,
            StdinOnce: false,
            HostConfig: {
                Memory: 100 * 1024 * 1024,
                CpuQuota: 50000,
                NetworkMode: "none",
                AutoRemove: false,
            },
        });

        const stream = await container.attach({
            stream: true,
            stdin: true,
            stdout: true,
            stderr: true,
            hijack: true, // <--- ADD THIS
        });

        let pythonIsReady = false;

        stream.on("data", (chunk) => {
            let output = chunk.toString();

            // 1. Check for the Ready Signal
            if (!pythonIsReady) {
                if (output.includes(READY_SIGNAL)) {
                    pythonIsReady = true;
                    // Remove the signal and anything BEFORE it (the Docker handshake)
                    output = output.split(READY_SIGNAL).pop();
                } else {
                    // Still receiving handshake/docker noise, ignore it
                    return;
                }
            }

            if (!output) return;

            // 2. Handle Input Signal
            if (output.includes(INPUT_SIGNAL)) {
                socket.emit("waiting_for_input");
                output = output.split(INPUT_SIGNAL).join("");
            }

            if (output.length > 0) {
                socket.emit("output", output);
            }
        });

        socket.on("stdin", (input) => {
            if (stream && stream.writable) {
                // IMPORTANT: When Tty: true, we must write exactly what
                // the user typed. Python is waiting for that newline.
                stream.write(input);
            }
        });

        await container.start();

        const waitPromise = container.wait();
        const timeoutPromise = new Promise((_, reject) => {
            timeoutHandle = setTimeout(() => {
                reject(new Error("Execution timeout"));
            }, 30000); // Increased to 30s so YOU have time to type!
        });

        await Promise.race([waitPromise, timeoutPromise]);
        clearTimeout(timeoutHandle);

        await container.remove({ force: true }).catch(() => {});
        socket.emit("finished");
        socket.disconnect();
    } catch (err) {
        if (timeoutHandle) clearTimeout(timeoutHandle);
        socket.emit("error", err.message);
        if (container) await container.remove({ force: true }).catch(() => {});
        socket.disconnect();
    } finally {
        currentlyRunning--;
        processQueue();
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
            log(`Queue full, adding to queue (${queue.length + 1} waiting)`);
            queue.push({ socket, data });
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
