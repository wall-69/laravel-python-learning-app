import "./bootstrap";

// Vue
import { createApp } from "vue";
import CodeRunnerComponent from "./components/CodeRunnerComponent.vue";
import CodeBlockComponent from "./components/CodeBlockComponent.vue";
import QuizComponent from "./components/QuizComponent.vue";
import QuizChoiceComponent from "./components/quiz/QuizChoiceComponent.vue";
import QuizAnswerComponent from "./components/quiz/QuizAnswerComponent.vue";
import QuizDragAndDropComponent from "./components/quiz/QuizDragAndDropComponent.vue";
import QuizDragComponent from "./components/quiz/QuizDragComponent.vue";
import QuizDropComponent from "./components/quiz/QuizDropComponent.vue";
import AdminEditorComponent from "./components/AdminEditorComponent.vue";

const app = createApp({});

app.config.compilerOptions.whitespace = "preserve";

app.component("code-runner", CodeRunnerComponent);
app.component("code-block", CodeBlockComponent);
app.component("quiz", QuizComponent);
app.component("quiz-choice", QuizChoiceComponent);
app.component("quiz-answer", QuizAnswerComponent);
app.component("quiz-drag-and-drop", QuizDragAndDropComponent);
app.component("quiz-drag", QuizDragComponent);
app.component("quiz-drop", QuizDropComponent);
app.component("admin-editor", AdminEditorComponent);

app.mount("#app");

// Monaco editor
import editorWorker from "monaco-editor/esm/vs/editor/editor.worker?worker";
globalThis.MonacoEnvironment = {
    getWorker(workerId, label) {
        switch (label) {
            default:
                return new editorWorker();
        }
    },
};
