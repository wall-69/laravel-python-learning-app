import templateHTML from "./html/QuizChoiceTool.html";
import quizChoiceAnswerHTML from "./html/QuizChoiceAnswer.html";

export default class QuizChoiceTool {
    static get toolbox() {
        return {
            title: "Choice",
            icon: "",
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

        this.question.innerText = this.data.question || "";
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

                answer.querySelector(".form-check-label").innerText =
                    savedAnswer.answer;
                answer.querySelector(".form-check-input").checked =
                    savedAnswer.correct;
                this.answersContainer.appendChild(answer);
            }
        }

        // Button for adding answers to question
        const addBtn = document.createElement("button");
        addBtn.innerText = "+";
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
                answer: answer.querySelector(".form-check-label").innerText,
                correct: answer.querySelector(".form-check-input").checked,
            });
        }

        return {
            question: this.question.innerText,
            explanation: this.explanation.innerHTML,
            answers: answers,
        };
    }
}
