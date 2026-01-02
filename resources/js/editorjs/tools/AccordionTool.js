import templateHTML from "./html/AccordionTool.html?raw";
import createAddButton from "./utils/addButton";

export default class AccordionTool {
    static get toolbox() {
        return {
            title: "Accordion",
            icon: "",
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.questions = [];
        this.accordionContainer = null;
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

    save(blockContent) {
        const items =
            this.accordionContainer.querySelectorAll(".accordion-item");
        this.questions = [];
        items.forEach((item) => {
            const question = item.querySelector(
                ".editor-accordion-question"
            ).innerHTML;
            const answer = item.querySelector(
                ".editor-accordion-answer"
            ).innerHTML;
            this.questions.push({ question, answer });
        });

        return {
            questions: this.questions,
        };
    }

    addAccordion(
        item = { question: "Otázka", answer: "Odpoveď" },
        index = null
    ) {
        const questionId = `accordion-${Math.random()}-item-${index + 1}`;
        const accordionItem = document.createElement("div");
        accordionItem.classList.add("accordion-item", "mb-2");
        accordionItem.innerHTML = `
                <h5 class="accordion-header">
                    <span class="editor-edittable editor-accordion-question" contenteditable="true">
                        ${item.question}
                    </span>
                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="this.closest('.accordion-item').remove()">X</button>
                </h5>
                <div id="${questionId}" class="" data-bs-parent="#accordion-1">
                    <div class="editor-edittable editor-accordion-answer" contenteditable="true">
                        ${item.answer}
                    </div>
                </div>
            `;
        this.accordionContainer.appendChild(accordionItem);
    }
}
