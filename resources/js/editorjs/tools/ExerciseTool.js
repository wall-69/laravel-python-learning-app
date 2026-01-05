import * as monaco from "monaco-editor";
import templateHTML from "./html/ExerciseTool.html?raw";
import createAddButton from "./utils/addButton";

export default class ExerciseBlock {
    static get toolbox() {
        return {
            title: "Exercise",
            icon: "",
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.editor = null;
        this.header = null;
        this.description = null;
        this.assignment = null;
        this.testsContainer = null;
    }

    static get sanitize() {
        return {
            header: true,
            description: true,
            assignment: true,
        };
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();
        wrapper.classList.add("mb-3");

        const editorContainer = wrapper.querySelector(".editor-exercise-code");
        const runButton = wrapper.querySelector("button");
        this.header = wrapper.querySelector(".editor-exercise-header");
        this.description = wrapper.querySelector(
            ".editor-exercise-description"
        );
        this.assignment = wrapper.querySelector(".editor-exercise-assignment");
        this.testsContainer = wrapper.querySelector(".editor-exercise-tests");

        this.header.innerHTML = this.data.header || "";
        this.description.innerHTML = this.data.description || "";
        this.assignment.innerHTML = this.data.assignment || "";

        // Description textarea keydown fix
        this.description.addEventListener("keydown", (e) => {
            e.stopPropagation();
        });

        // Assignment textarea keydown fix
        this.assignment.addEventListener("keydown", (e) => {
            e.stopPropagation();
        });

        // Load saved tests
        if (this.data.tests) {
            for (let test of JSON.parse(this.data.tests)) {
                this.addTest(test.raw);
            }
        }

        // Button for adding tests to exercise
        const addBtn = createAddButton();
        addBtn.addEventListener("click", (e) => {
            e.preventDefault();

            this.addTest();

            // Move the add button to the bottom of the DOM
            this.testsContainer.appendChild(addBtn);
        });
        this.testsContainer.appendChild(addBtn);

        // Fake code submit
        runButton.addEventListener("click", (e) => {
            e.preventDefault();
        });

        this.editor = monaco.editor.create(editorContainer, {
            value: this.data.code || "",
            language: "python",
            automaticLayout: true,
            minimap: { enabled: false },
        });

        return wrapper;
    }

    save(blockContent) {
        let tests = [];
        for (let test of this.testsContainer.querySelectorAll(
            ".editor-exercise-test"
        )) {
            tests.push(this.parseTestCase(test.innerText));
        }

        return {
            header: this.header.innerHTML.trim(),
            description: this.description.innerHTML.trim(),
            assignment: this.assignment.innerHTML.trim(),
            tests: JSON.stringify(tests),
            code: this.editor.getValue(),
        };
    }

    addTest(test = "") {
        let testContainer = document.createElement("test");
        testContainer.classList.add(
            "d-flex",
            "justify-content-between",
            "align-items-center",
            "mb-1"
        );

        let testCase = document.createElement("div");
        testCase.classList.add("editor-editable", "editor-exercise-test");
        testCase.contentEditable = true;
        testCase.innerHTML = test || "Zadajte test";

        // Control buttons wrapper
        let controls = document.createElement("div");
        controls.classList.add("d-flex", "gap-1");

        // Up button
        let upBtn = document.createElement("button");
        upBtn.classList.add("btn", "btn-secondary", "btn-sm");
        upBtn.innerHTML =
            "<i class='bx bx-arrow-up fst-normal d-flex align-items-center'></i>";
        upBtn.addEventListener("click", (e) => {
            e.preventDefault();

            let prev = testContainer.previousElementSibling;
            if (prev) {
                this.testsContainer.insertBefore(testContainer, prev);
            }
        });

        // Down button
        let downBtn = document.createElement("button");
        downBtn.classList.add("btn", "btn-secondary", "btn-sm");
        downBtn.innerHTML =
            "<i class='bx bx-arrow-down fst-normal d-flex align-items-center'></i>";
        downBtn.addEventListener("click", (e) => {
            e.preventDefault();

            let next = testContainer.nextElementSibling;
            if (next) {
                this.testsContainer.insertBefore(next, testContainer);
            }
        });

        // Delete button
        let deleteBtn = document.createElement("button");
        deleteBtn.classList.add("btn", "btn-danger", "btn-sm");
        deleteBtn.innerHTML = "X";
        deleteBtn.addEventListener("click", (e) => {
            e.preventDefault();

            testContainer.remove();
        });

        controls.appendChild(upBtn);
        controls.appendChild(downBtn);
        controls.appendChild(deleteBtn);

        testContainer.appendChild(testCase);
        testContainer.appendChild(controls);

        this.testsContainer.appendChild(testContainer);
    }

    parseTestCase(testCase) {
        testCase = testCase.trim();

        // 1. Variable comparison: x ## y
        const comparisons = ["==", "!=", "<=", ">=", "<", ">"];
        for (let op of comparisons) {
            let idx = testCase.indexOf(op);
            if (idx == -1) {
                continue;
            }

            let variable = testCase.slice(0, idx).trim();
            let expected = testCase.slice(idx + op.length).trim();

            return {
                type: "variable",
                raw: testCase,
                variable: variable,
                operator: op,
                expected: expected,
            };
        }

        // 2. Function call: func(args) returns x
        if (testCase.includes("returns")) {
            let [callPart, expectedPart] = testCase.split("returns");
            let nameEnd = callPart.indexOf("(");
            let name = callPart.slice(0, nameEnd).trim();
            let argsString = callPart
                .slice(nameEnd + 1, callPart.lastIndexOf(")"))
                .trim();
            let args = argsString
                ? argsString.split(",").map((a) => a.trim())
                : [];

            return {
                type: "function",
                raw: testCase,
                name: name,
                args: args,
                expected: expectedPart.trim(),
            };
        }

        // 3. Print check: "...something..." was printed
        if (testCase.endsWith("was printed")) {
            let expected = testCase.replace("was printed", "").trim();

            return {
                type: "print",
                raw: testCase,
                expected: expected,
            };
        }

        // 4. Type check: x is type
        if (testCase.includes(" is ")) {
            let [name, expected] = testCase.split(" is ").map((s) => s.trim());

            return {
                type: "type",
                raw: testCase,
                name: name,
                expected: expected,
            };
        }

        // Unknown
        return {
            type: "unknown",
            raw: testCase,
        };
    }
}
