const io = require("socket.io-client");

const TEST_CODE = 'print("Ahoj, svet!")';
const EXPECTED_OUTPUT = "Ahoj, svet!\r\n";
const NUM_TESTS = 200;

let completed = 0;
let failed = 0;
let failedTests = [];

function runTest(testNumber) {
    return new Promise((resolve) => {
        const socket = io("http://localhost:3000", { reconnection: false });
        let output = "";
        let hasUnexpectedJson = false;

        socket.emit("run", { code: TEST_CODE });

        socket.on("output", (data) => {
            output += data;

            // Check for the problematic JSON
            if (data.includes('"stream"') || data.includes('"stdin"')) {
                hasUnexpectedJson = true;
            }
        });

        socket.on("disconnect", () => {
            completed++;

            // Check if output matches expected
            if (hasUnexpectedJson || output !== EXPECTED_OUTPUT) {
                failed++;
                failedTests.push({
                    testNumber,
                    output,
                    hasUnexpectedJson,
                });
                console.log(`❌ Test ${testNumber} FAILED`);
                console.log(`   Output: ${JSON.stringify(output)}`);
            } else {
                console.log(`✅ Test ${testNumber} passed`);
            }

            resolve();
        });
    });
}

async function runAllTests() {
    console.log(`Starting ${NUM_TESTS} tests...\n`);
    const startTime = Date.now();

    // Run tests in batches to avoid overwhelming the server
    const BATCH_SIZE = 20;
    for (let i = 0; i < NUM_TESTS; i += BATCH_SIZE) {
        const batch = [];
        for (let j = 0; j < BATCH_SIZE && i + j < NUM_TESTS; j++) {
            batch.push(runTest(i + j + 1));
        }
        await Promise.all(batch);
    }

    const duration = ((Date.now() - startTime) / 1000).toFixed(2);

    console.log("\n" + "=".repeat(50));
    console.log(`RESULTS:`);
    console.log(`Total tests: ${NUM_TESTS}`);
    console.log(`Passed: ${completed - failed}`);
    console.log(`Failed: ${failed}`);
    console.log(
        `Success rate: ${(((completed - failed) / NUM_TESTS) * 100).toFixed(
            2
        )}%`
    );
    console.log(`Duration: ${duration}s`);
    console.log("=".repeat(50));

    if (failed > 0) {
        console.log("\n❌ FAILED TESTS:");
        failedTests.forEach((test) => {
            console.log(`\nTest #${test.testNumber}:`);
            console.log(`  Output: ${JSON.stringify(test.output)}`);
            console.log(`  Has unexpected JSON: ${test.hasUnexpectedJson}`);
        });
    } else {
        console.log("\n🎉 ALL TESTS PASSED!");
    }

    process.exit(failed > 0 ? 1 : 0);
}

runAllTests();
