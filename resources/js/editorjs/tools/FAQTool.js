import templateHTML from "./html/FAQTool.html?raw";
import createAddButton from "./utils/addButton";

export default class FAQTool {
    static get toolbox() {
        return {
            title: "FAQ",
            icon: "",
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.questions = [];
        this.faqContainer = null;
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();
        wrapper.classList.add("mb-3");

        this.faqContainer = wrapper.querySelector(".editor-faq");

        this.data.questions?.forEach((item, index) => {
            this.addFAQ(item, index + 1);
        });

        // Button for adding FAQ items
        const addBtn = createAddButton();
        addBtn.addEventListener("click", (e) => {
            e.preventDefault();

            this.addFAQ();

            // Move the add button to the bottom of the DOM
            this.faqContainer.appendChild(addBtn);
        });
        this.faqContainer.appendChild(addBtn);

        return wrapper;
    }

    save(blockContent) {
        const items = this.faqContainer.querySelectorAll(".accordion-item");
        this.questions = [];
        items.forEach((item) => {
            const question = item.querySelector(
                ".editor-faq-question"
            ).innerHTML;
            const answer = item.querySelector(".editor-faq-answer").innerHTML;
            this.questions.push({ question, answer });
        });

        return {
            questions: this.questions,
        };
    }

    addFAQ(item = { question: "Otázka", answer: "Odpoveď" }, index = null) {
        const questionId = `faq-${Math.random()}-item-${index + 1}`;
        const accordionItem = document.createElement("div");
        accordionItem.classList.add("accordion-item", "mb-2");
        accordionItem.innerHTML = `
                <h5 class="accordion-header">
                    <span class="editor-edittable editor-faq-question" contenteditable="true">
                        ${item.question}
                    </span>
                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="this.closest('.accordion-item').remove()">X</button>
                </h5>
                <div id="${questionId}" class="" data-bs-parent="#faq-1">
                    <div class="editor-edittable editor-faq-answer" contenteditable="true">
                        ${item.answer}
                    </div>
                </div>
            `;
        this.faqContainer.appendChild(accordionItem);
    }
}
