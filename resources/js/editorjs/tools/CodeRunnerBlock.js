import * as monaco from "monaco-editor";

export default class CodeRunnerBlock {
    static get toolbox() {
        return {
            title: "Code Runner",
            icon: '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 8L3 11.6923L7 16M17 8L21 11.6923L17 16M14 4L10 20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>',
        };
    }

    constructor({ data }) {
        this.data = data || {};
        this.editor = null;
    }

    render() {
        const wrapper = document.createElement("div");

        wrapper.innerHTML = `
            <h2 class="editor-code-runner-header" contenteditable="true">
                ${this.data.header || "Edit heading..."}
            </h2>

            <p class="editor-code-runner-description" contenteditable="true">
                ${this.data.description || "Edit description..."}
            </p>
            <div class="editor-code-runner border" style="width: 100%; height: 300px;"></div>
            <button class="mt-1 btn btn-primary">Spustiť</button>

            <h3 class="mt-3">Výstup</h3>
            <textarea class="form-control" style="width: 100%; height: 200px;" readonly></textarea>
        `;

        const editorContainer = wrapper.querySelector(".editor-code-runner");
        this.output = wrapper.querySelector("textarea");
        this.runButton = wrapper.querySelector("button");

        this.headerEl = wrapper.querySelector(".editor-code-runner-header");
        this.descriptionEl = wrapper.querySelector(
            ".editor-code-runner-description"
        );
        this.headerEl.innerText = this.data.header || "";
        this.descriptionEl.innerText = this.data.description || "";

        // Fake code submit
        this.runButton.addEventListener("click", () => {
            this.output.value = "Test: [output]";
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
            header: this.headerEl.innerText.trim(),
            description: this.descriptionEl.innerText.trim(),
            code: this.editor.getValue(),
        };
    }
}
