import templateHTML from "./html/QuizDragAndDropTool.html";
import dragTemplateHTML from "./html/QuizDragTool.html";
import dropTemplateHTML from "./html/QuizDropTool.html";

export default class QuizDragAndDropTool {
    static get toolbox() {
        return {
            title: "Drag & Drop",
            icon: "",
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.question = this.data.question || "";
        this.explanation = this.data.explanation || "";
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();

        // Question & explanation
        this.question = wrapper.querySelector(".editor-quiz-dnd-question");
        this.explanation = wrapper.querySelector(
            ".editor-quiz-dnd-explanation"
        );

        this.question.innerText = this.data.question || "";
        this.explanation.innerHTML = this.data.explanation || "";

        // Drags & drops
        const answersContainer = wrapper.querySelector(
            ".editor-quiz-dnd-answers"
        );

        const addBtn = document.createElement("button");
        addBtn.innerText = "+";
        addBtn.addEventListener("click", (e) => {
            const drop = document.createElement("div");
            drop.innerHTML = dropTemplateHTML.trim();
            const drag = document.createElement("div");
            drag.innerHTML = dragTemplateHTML.trim();

            // Button for deleting this answer
            const deleteBtn = document.createElement("button");
            deleteBtn.innerText = "x";
            deleteBtn.addEventListener("click", (e) => {
                deleteBtn.remove();
                drop.remove();
                drag.remove();
            });

            answersContainer.appendChild(deleteBtn);
            answersContainer.appendChild(drop);
            answersContainer.appendChild(drag);

            // Move the add button to the bottom of the DOM
            answersContainer.appendChild(addBtn);
        });
        answersContainer.appendChild(addBtn);

        return wrapper;
    }

    save(blockContent) {
        return {};
    }
}
