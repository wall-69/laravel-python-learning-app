import templateHTML from "./html/QuizChoiceTool.html";
import quizChoiceAnswerHTML from "./html/QuizChoiceAnswer.html";
import createAddButton from "./utils/addButton";

export default class QuizChoiceTool {
    static get toolbox() {
        return {
            title: "Choice",
            icon: '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h10m-10 7h10m-10 7h10"></path><rect width="4" height="4" x="3" y="3" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" rx="1"></rect><rect width="4" height="4" x="3" y="10" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" rx="1"></rect><rect width="4" height="4" x="3" y="17" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" rx="1"></rect></g></svg>',
        };
    }

    constructor({ data }) {
        this.data = data || {};

        this.question = this.data.question || "";
        this.explanation = this.data.explanation || "";
        this.answersContainer = null;
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();

        // Question & explanation
        this.question = wrapper.querySelector(".editor-quiz-choice-question");
        this.explanation = wrapper.querySelector(
            ".editor-quiz-choice-explanation"
        );

        this.question.innerHTML = this.data.question || "";
        this.explanation.innerHTML = this.data.explanation || "";

        // Answers container
        this.answersContainer = wrapper.querySelector(
            ".editor-quiz-choice-answers"
        );

        // Load saved answers
        if (this.data.answers) {
            for (let savedAnswer of this.data.answers) {
                const answer = document.createElement("div");
                answer.innerHTML = quizChoiceAnswerHTML.trim();

                answer.querySelector(".form-check-label").innerHTML =
                    savedAnswer.answer;
                answer.querySelector(".form-check-input").checked =
                    savedAnswer.correct;
                this.answersContainer.appendChild(answer);
            }
        }

        // Button for adding answers to question
        const addBtn = createAddButton();
        addBtn.addEventListener("click", (e) => {
            const answer = document.createElement("div");
            answer.innerHTML = quizChoiceAnswerHTML.trim();

            // Button for deleting this answer
            const deleteBtn = answer.querySelector(
                ".editor-quiz-choice-answer-delete"
            );
            deleteBtn.addEventListener("click", (e) => {
                answer.remove();
            });

            this.answersContainer.appendChild(answer);

            // Move the add button to the bottom of the DOM
            this.answersContainer.appendChild(addBtn);
        });
        this.answersContainer.appendChild(addBtn);

        return wrapper;
    }

    save(blockContent) {
        const answers = [];

        // Save all answers for this question and their correctness
        for (let i = 0; i < this.answersContainer.childElementCount - 1; i++) {
            const answer = this.answersContainer.children[i];
            answers.push({
                answer: answer.querySelector(".editor-quiz-choice-answer-text")
                    .innerHTML,
                correct: answer.querySelector(
                    ".editor-quiz-choice-answer-correct"
                ).checked,
            });
        }

        return {
            question: this.question.innerHTML,
            explanation: this.explanation.innerHTML,
            answers: answers,
        };
    }
}
