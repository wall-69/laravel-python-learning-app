const io = require("socket.io-client");

const SERVER_URL = "http://localhost:3000";

/**
 * Helper to run a single test
 */
function runTest({ name, code, expect }) {
    return new Promise((resolve) => {
        const socket = io(SERVER_URL, { reconnection: false });
        let output = "";
        let errorMsg = null;
        let waitingForInput = false;

        socket.emit("run", { code });

        socket.on("output", (data) => {
            output += data;
        });

        socket.on("error", (err) => {
            errorMsg = err;
        });

        socket.on("waiting_for_input", () => {
            waitingForInput = true;
            socket.emit("stdin", "42\n");
        });

        socket.on("disconnect", () => {
            let passed = true;
            let reason = "";

            if (expect.output !== undefined && output !== expect.output) {
                passed = false;
                reason = `Output mismatch\nExpected: ${JSON.stringify(
                    expect.output
                )}\nGot: ${JSON.stringify(output)}`;
            }

            if (expect.error !== undefined && errorMsg !== expect.error) {
                passed = false;
                reason = `Error mismatch\nExpected: "${expect.error}"\nGot: "${errorMsg}"`;
            }

            if (expect.waitingForInput && !waitingForInput) {
                passed = false;
                reason = `Expected input request but none occurred`;
            }

            resolve({
                name,
                passed,
                output,
                error: errorMsg,
                reason,
            });
        });
    });
}

/**
 * Test definitions
 */
const TESTS = [
    {
        name: "Basic hello world",
        code: `print("Hello, world!")`,
        expect: {
            output: "Hello, world!\r\n",
        },
    },

    {
        name: "Python runtime error",
        code: `raise Exception("boom")`,
        expect: {
            outputIncludes: "Exception: boom",
        },
    },

    {
        name: "Timeout exceeded",
        code: `
import time
while True:
    time.sleep(1)
        `,
        expect: {
            error: "Terminated: Timeout - your program took too long to run.",
        },
    },

    {
        name: "Memory limit exceeded (OOM)",
        code: `
a = []
while True:
    a.append("x" * 10_000_000)
        `,
        expect: {
            error: "Terminated: Memory limit exceeded - your program used too much memory.",
        },
    },

    {
        name: "Max output exceeded",
        code: `
while True:
    print("X" * 1000)
        `,
        expect: {
            error: "Terminated: Max output size exceeded.",
        },
    },

    {
        name: "stdin handling",
        code: `
x = input()
print(int(x) * 2)
        `,
        expect: {
            output: "42\r\n84\r\n",
            waitingForInput: true,
        },
    },

    {
        name: "Deep recursion (stack overflow style)",
        code: `
def f():
    return f()
f()
    `,
        expect: {
            outputIncludes: "RecursionError",
        },
    },

    {
        name: "Large but valid output (under limit)",
        code: `
print("A" * 100_000)
        `,
        expect: {
            output: "A".repeat(100_000) + "\r\n",
        },
    },
];

/**
 * Run all tests sequentially (important for determinism)
 */
async function runAllTests() {
    console.log(`Running ${TESTS.length} integration tests...\n`);

    let passed = 0;
    const failed = [];

    for (const test of TESTS) {
        const result = await runTest(test);

        if (result.passed) {
            console.log(`✅ ${result.name}`);
            passed++;
        } else {
            console.log(`❌ ${result.name}`);
            console.log(result.reason);
            failed.push(result);
        }
    }

    console.log("\n" + "=".repeat(60));
    console.log(`RESULTS`);
    console.log(`Passed: ${passed}`);
    console.log(`Failed: ${failed.length}`);
    console.log(`Success rate: ${((passed / TESTS.length) * 100).toFixed(2)}%`);
    console.log("=".repeat(60));

    if (failed.length > 0) {
        console.log("\n❌ FAILED TEST DETAILS:");
        failed.forEach((f) => {
            console.log(`\nTest: ${f.name}`);
            console.log(`Output: ${JSON.stringify(f.output)}`);
            console.log(`Error: ${JSON.stringify(f.error)}`);
        });
        process.exit(1);
    } else {
        console.log("\n🎉 ALL TESTS PASSED");
        process.exit(0);
    }
}

runAllTests();
