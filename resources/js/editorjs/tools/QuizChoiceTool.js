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

        this.question = "";
        this.answersContainer = null;
        this.explanation = "";
    }

    render() {
        const wrapper = document.createElement("div");
        wrapper.innerHTML = templateHTML.trim();

        this.question = wrapper.querySelector(".editor-quiz-choice-question");
        this.explanation = wrapper.querySelector(
            ".editor-quiz-choice-explanation"
        );

        this.answersContainer = wrapper.querySelector(
            ".editor-quiz-choice-answers"
        );
        const btn = document.createElement("button");
        btn.innerText = "+";
        btn.addEventListener("click", (e) => {
            const answer = document.createElement("div");
            answer.innerHTML = quizChoiceAnswerHTML.trim();

            const deleteBtn = answer.querySelector(
                ".editor-quiz-choice-answer-delete"
            );
            deleteBtn.classList.add("ms-auto");
            deleteBtn.addEventListener("click", (e) => {
                answer.remove();
            });
            this.answersContainer.appendChild(answer);

            this.answersContainer.appendChild(btn);
        });
        this.answersContainer.appendChild(btn);

        return wrapper;
    }

    save(blockContent) {
        const answers = [];

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
