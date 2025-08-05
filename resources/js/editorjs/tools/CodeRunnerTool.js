import * as monaco from "monaco-editor";
import templateHTML from "./html/CodeRunnerTool.html";

export default class CodeRunnerBlock {
    static get toolbox() {
        return {
            title: "Code Runner",
            icon: '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 19H21M3 5L11 12L3 19" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>',
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.editor = null;
        this.header = null;
        this.description = null;
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();

        const editorContainer = wrapper.querySelector(".editor-code-runner");
        const output = wrapper.querySelector("textarea");
        const runButton = wrapper.querySelector("button");
        this.header = wrapper.querySelector(".editor-code-runner-header");
        this.description = wrapper.querySelector(
            ".editor-code-runner-description"
        );

        this.header.innerText = this.data.header || "";
        this.description.innerText = this.data.description || "";

        // Fake code submit
        runButton.addEventListener("click", (e) => {
            e.preventDefault();
            output.value = "Test: [výstup]";
        });

        this.editor = monaco.editor.create(editorContainer, {
            value: this.data.code || 'print("Ahoj, svet!")',
            language: "python",
            automaticLayout: true,
            minimap: { enabled: false },
        });

        return wrapper;
    }

    save(blockContent) {
        return {
            header: this.header.innerText.trim(),
            description: this.description.innerText.trim(),
            code: this.editor.getValue(),
        };
    }
}
