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

        stream.on("data", (chunk) => {
            let output = chunk.toString();

            output = output.replace(/^.*\{.*"hijack":true\}/s, "");

            // Handle input signal
            if (output.includes(INPUT_SIGNAL)) {
                if (timeoutHandle) clearTimeout(timeoutHandle);
                socket.emit("waiting_for_input");
                output = output.split(INPUT_SIGNAL).join("");
            }

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

        socket.emit("error", err.message);

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
