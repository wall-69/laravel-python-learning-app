import templateHTML from "./html/QuizDragAndDropTool.html";
import dragTemplateHTML from "./html/QuizDragTool.html";
import dropTemplateHTML from "./html/QuizDropTool.html";
import createAddButton from "./utils/addButton";

export default class QuizDragAndDropTool {
    static get toolbox() {
        return {
            title: "Drag & Drop",
            icon: '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M16.1924 5.65683C16.5829 5.2663 16.5829 4.63314 16.1924 4.24261L13.364 1.41419C12.5829 0.633139 11.3166 0.633137 10.5355 1.41419L7.70711 4.24261C7.31658 4.63314 7.31658 5.2663 7.70711 5.65683C8.09763 6.04735 8.73079 6.04735 9.12132 5.65683L11 3.77812V11.0503H3.72784L5.60655 9.17157C5.99707 8.78104 5.99707 8.14788 5.60655 7.75735C5.21602 7.36683 4.58286 7.36683 4.19234 7.75735L1.36391 10.5858C0.582863 11.3668 0.582859 12.6332 1.36391 13.4142L4.19234 16.2426C4.58286 16.6332 5.21603 16.6332 5.60655 16.2426C5.99707 15.8521 5.99707 15.219 5.60655 14.8284L3.8284 13.0503H11V20.2219L9.12132 18.3432C8.73079 17.9526 8.09763 17.9526 7.7071 18.3432C7.31658 18.7337 7.31658 19.3669 7.7071 19.7574L10.5355 22.5858C11.3166 23.3669 12.5829 23.3669 13.364 22.5858L16.1924 19.7574C16.5829 19.3669 16.5829 18.7337 16.1924 18.3432C15.8019 17.9526 15.1687 17.9526 14.7782 18.3432L13 20.1213V13.0503H20.071L18.2929 14.8284C17.9024 15.219 17.9024 15.8521 18.2929 16.2426C18.6834 16.6332 19.3166 16.6332 19.7071 16.2426L22.5355 13.4142C23.3166 12.6332 23.3166 11.3668 22.5355 10.5858L19.7071 7.75735C19.3166 7.36683 18.6834 7.36683 18.2929 7.75735C17.9024 8.14788 17.9024 8.78104 18.2929 9.17157L20.1716 11.0503H13V3.87867L14.7782 5.65683C15.1687 6.04735 15.8019 6.04735 16.1924 5.65683Z" fill="#0F0F0F"></path> </g></svg>',
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.question = this.data.question || "";
        this.explanation = this.data.explanation || "";

        this.answersContainer = null;
        this.addBtn = null;
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

        // Answers container
        this.answersContainer = wrapper.querySelector(
            ".editor-quiz-dnd-answers"
        );

        // Load saved pairs
        if (this.data.pairs) {
            for (let savedPair of this.data.pairs) {
                const row = this.addAnswerRow();

                row.querySelector(".editor-quiz-dnd-drop").innerHTML =
                    savedPair.drop;
                row.querySelector(".editor-quiz-dnd-drag").innerHTML =
                    savedPair.drag;
            }
        }

        // Add button
        this.addBtn = createAddButton();
        this.addBtn.addEventListener("click", (e) => {
            e.preventDefault();
            this.addAnswerRow();
        });
        this.answersContainer.appendChild(this.addBtn);

        return wrapper;
    }

    addAnswerRow() {
        const row = document.createElement("div");
        row.classList.add("row", "g-2", "mb-2", "align-items-center");

        // Delete button
        const deleteCol = document.createElement("div");
        deleteCol.classList.add("col-1");

        const deleteBtn = document.createElement("button");
        deleteBtn.classList.add("btn", "btn-danger", "btn-sm");
        deleteBtn.innerHTML = "X";

        deleteBtn.addEventListener("click", (e) => {
            e.preventDefault();
            row.remove();
        });

        deleteCol.appendChild(deleteBtn);

        // Drop col
        const dropCol = document.createElement("div");
        dropCol.classList.add("col-5");
        dropCol.innerHTML = dropTemplateHTML.trim();

        // Drag col
        const dragCol = document.createElement("div");
        dragCol.classList.add("col-6");
        dragCol.innerHTML = dragTemplateHTML.trim();

        // Append
        row.appendChild(deleteCol);
        row.appendChild(dropCol);
        row.appendChild(dragCol);

        // Insert row before the add button
        this.answersContainer.insertBefore(row, this.addBtn);

        return row;
    }

    save(blockContent) {
        // Collect all drag/drop pairs
        const pairs = [];

        for (let i = 0; i < this.answersContainer.childElementCount - 1; i++) {
            const row = this.answersContainer.children[i];

            const drop = row.querySelector(".editor-quiz-dnd-drop");
            const drag = row.querySelector(".editor-quiz-dnd-drag");

            pairs.push({
                drop: drop.innerText || "",
                drag: drag.innerText || "",
            });
        }

        return {
            question: this.question.innerText,
            explanation: this.explanation.innerHTML,
            pairs: pairs,
        };
    }
}
