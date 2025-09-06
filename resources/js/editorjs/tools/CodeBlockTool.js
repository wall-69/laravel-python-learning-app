import * as monaco from "monaco-editor";
import templateHTML from "./html/CodeBlockTool.html";

export default class CodeBlockTool {
    static get toolbox() {
        return {
            title: "Code Block",
            icon: '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 8L3 11.6923L7 16M17 8L21 11.6923L17 16M14 4L10 20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></g></svg>',
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
        wrapper.classList.add("mb-3");

        const editorContainer = wrapper.querySelector(".editor-code-block");
        this.header = wrapper.querySelector(".editor-code-block-header");
        this.description = wrapper.querySelector(
            ".editor-code-block-description"
        );

        this.header.innerHTML = this.data.header || "";
        this.description.innerHTML = this.data.description || "";

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
            header: this.header.innerHTML.trim(),
            description: this.description.innerHTML.trim(),
            code: this.editor.getValue(),
        };
    }
}
