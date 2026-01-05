import templateHTML from "./html/AccordionTool.html?raw";
import createAddButton from "./utils/addButton";
import EditorJS from "@editorjs/editorjs";

export default class AccordionTool {
    static get toolbox() {
        return {
            title: "Accordion",
            icon: "",
        };
    }

    constructor({ data, config }) {
        this.data = data || {};

        this.questions = [];
        this.accordionContainer = null;
        this.editors = new Map(); // Map<HTMLElement, EditorJS>

        this.accordionTools = (config && config.tools) || {};
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();
        wrapper.classList.add("mb-3");

        this.accordionContainer = wrapper.querySelector(".editor-accordion");

        this.data.questions?.forEach((item, index) => {
            this.addAccordion(item, index + 1);
        });

        // Button for adding accordion questions
        const addBtn = createAddButton();
        addBtn.addEventListener("click", (e) => {
            e.preventDefault();

            this.addAccordion();

            // Move the add button to the bottom of the DOM
            this.accordionContainer.appendChild(addBtn);
        });
        this.accordionContainer.appendChild(addBtn);

        return wrapper;
    }

    async save(blockContent) {
        const items =
            this.accordionContainer.querySelectorAll(".accordion-item");
        this.questions = [];

        for (const item of items) {
            const question = item.querySelector(
                ".editor-accordion-question"
            ).innerHTML;

            let answerBlocks = null;
            const editor = this.editors.get(item);
            if (editor) {
                try {
                    const saved = await editor.save();
                    answerBlocks = saved && saved.blocks ? saved.blocks : [];
                } catch (err) {
                    // fallback to raw html if save fails
                    const raw = item.querySelector(
                        ".editor-accordion-answer"
                    ).innerHTML;
                    answerBlocks = [{ type: "paragraph", data: { text: raw } }];
                }
            } else {
                const raw = item.querySelector(
                    ".editor-accordion-answer"
                ).innerHTML;
                answerBlocks = [{ type: "paragraph", data: { text: raw } }];
            }

            this.questions.push({ question, answer: answerBlocks });
        }

        return {
            questions: this.questions,
        };
    }

    addAccordion(
        item = { question: "Otázka", answer: "Odpoveď" },
        index = null
    ) {
        const questionId = `accordion-${Math.random()}-item-${
            (index || 0) + 1
        }`;
        const accordionItem = document.createElement("div");
        accordionItem.classList.add("accordion-item", "mb-2");

        accordionItem.innerHTML = `
			<h5 class="accordion-header">
				<span class="editor-editable editor-accordion-question" contenteditable="true">
					${item.question}
				</span>
				<button type="button" class="btn btn-sm btn-danger ms-2 remove-accordion">X</button>
			</h5>
			<div id="${questionId}" class="" data-bs-parent="#accordion-1">
				<div class="editor-accordion-answer"></div>
			</div>
		`;

        // append item first so EditorJS can attach to holder
        this.accordionContainer.appendChild(accordionItem);

        // setup remove button to destroy nested editor first
        const removeBtn = accordionItem.querySelector(".remove-accordion");
        removeBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const itemEl = e.target.closest(".accordion-item");
            const editorInstance = this.editors.get(itemEl);
            if (
                editorInstance &&
                typeof editorInstance.destroy === "function"
            ) {
                editorInstance.destroy();
            }
            this.editors.delete(itemEl);
            itemEl.remove();
        });

        // Create holder for EditorJS
        const answerHolder = accordionItem.querySelector(
            ".editor-accordion-answer"
        );
        // Create a single child holder with unique id for EditorJS
        const holderId = `editor-accordion-holder-${Math.random()
            .toString(36)
            .slice(2)}`;
        const holder = document.createElement("div");
        holder.id = holderId;
        answerHolder.appendChild(holder);

        // Prepare initial data for EditorJS (if item.answer is already blocks)
        let initialData = { blocks: [] };
        if (Array.isArray(item.answer)) {
            initialData.blocks = item.answer;
        } else if (typeof item.answer === "string") {
            initialData.blocks = [
                { type: "paragraph", data: { text: item.answer } },
            ];
        } else if (item.answer && item.answer.blocks) {
            initialData = item.answer;
        }

        const toolsConfig = this.accordionTools || {};

        // Initialize nested EditorJS
        let nestedEditor;
        try {
            nestedEditor = new EditorJS({
                holder: holderId,
                data: initialData,
                tools: toolsConfig,
                readOnly: false,
            });

            // Prevent inner editor from propagating keydown events to outer editor
            holder.addEventListener("keydown", (e) => {
                e.stopPropagation();
            });
        } catch (err) {
            // If EditorJS init fails, fallback: render initial HTML inside holder
            holder.innerHTML = initialData.blocks
                .map((b) => (b.data && b.data.text ? b.data.text : ""))
                .join("");
        }

        if (nestedEditor) {
            this.editors.set(accordionItem, nestedEditor);
        }
    }

    // ensure nested editors are destroyed when the tool is removed
    destroy() {
        for (const ed of this.editors.values()) {
            if (ed && typeof ed.destroy === "function") {
                try {
                    ed.destroy();
                } catch (e) {
                    // ignore individual destroy errors
                }
            }
        }
        this.editors.clear();
    }
}
