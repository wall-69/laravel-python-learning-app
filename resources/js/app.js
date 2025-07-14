import "./bootstrap";

// Vue
import { createApp } from "vue";
import CodeRunnerComponent from "./components/CodeRunnerComponent.vue";
import QuizComponent from "./components/QuizComponent.vue";
import QuizChoiceComponent from "./components/quiz/QuizChoiceComponent.vue";
import QuizAnswer from "./components/quiz/QuizAnswer.vue";

// Monaco editor
import * as monaco from "monaco-editor";
import editorWorker from "monaco-editor/esm/vs/editor/editor.worker?worker";

// Vue
const app = createApp({});

app.component("code-runner", CodeRunnerComponent);
app.component("quiz", QuizComponent);
app.component("quiz-choice", QuizChoiceComponent);
app.component("quiz-answer", QuizAnswer);

app.mount("#app");

// Monaco editor
globalThis.MonacoEnvironment = {
    getWorker(workerId, label) {
        switch (label) {
            default:
                return new editorWorker();
        }
    },
};
