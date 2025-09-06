import templateHTML from "./html/QuizTool.html?raw";

import EditorJS from "@editorjs/editorjs";
import QuizChoiceTool from "./QuizChoiceTool";
import QuizDragAndDropTool from "./QuizDragAndDropTool";

export default class QuizTool {
    static get toolbox() {
        return {
            title: "Quiz",
            icon: '<svg fill="#000000" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 403.48 403.48" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M277.271,0H46.176v403.48h311.129V80.035L277.271,0z M281.664,25.607l50.033,50.034h-50.033V25.607z M61.176,388.48V15 h205.489v75.641h75.641v297.84H61.176z"></path> <path d="M101.439,117.58h55.18V62.4h-55.18V117.58z M116.439,77.4h25.18v25.18h-25.18V77.4z"></path> <path d="M101.439,192.08h55.18V136.9h-55.18V192.08z M116.439,151.9h25.18v25.18h-25.18V151.9z"></path> <path d="M101.439,266.581h55.18V211.4h-55.18V266.581z M116.439,226.4h25.18v25.181h-25.18V226.4z"></path> <path d="M101.439,341.081h55.18v-55.18h-55.18V341.081z M116.439,300.901h25.18v25.18h-25.18V300.901z"></path> <rect x="177.835" y="326.081" width="114.688" height="15"></rect> <rect x="177.835" y="251.581" width="114.688" height="15"></rect> <rect x="177.835" y="177.08" width="114.688" height="15"></rect> <rect x="177.835" y="102.58" width="114.688" height="15"></rect> </g> </g></svg>',
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.innerEditor = null;
        this.header = null;
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();
        wrapper.classList.add("mb-3");

        const form = wrapper.firstElementChild;
        form.addEventListener("submit", (e) => e.preventDefault());

        const innerEditorContainer = wrapper.querySelector(".inner-editor");
        innerEditorContainer.id = "inner-editor-" + Date.now();
        this.header = wrapper.querySelector(".editor-quiz-header");

        // Load saved header
        this.header.innerHTML = this.data.header || "";

        this.innerEditor = new EditorJS({
            holder: innerEditorContainer.id,
            autofocus: true,
            logLevel: "WARN",

            tools: {
                quizChoice: {
                    class: QuizChoiceTool,
                    inlineToolbar: true,
                },
                quizDragAndDrop: {
                    class: QuizDragAndDropTool,
                    inlineToolbar: true,
                },
            },

            data: this.data.questions,
        });

        return wrapper;
    }

    async save(blockContent) {
        const questions = await this.innerEditor.save();

        return {
            header: this.header.innerHTML.trim(),
            questions: questions,
        };
    }
}
