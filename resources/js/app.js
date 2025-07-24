import "./bootstrap";

// Vue
import { createApp } from "vue";
import CodeRunnerComponent from "./components/CodeRunnerComponent.vue";
import QuizComponent from "./components/QuizComponent.vue";
import QuizChoiceComponent from "./components/quiz/QuizChoiceComponent.vue";
import QuizAnswerComponent from "./components/quiz/QuizAnswerComponent.vue";
import QuizDragAndDropComponent from "./components/quiz/QuizDragAndDropComponent.vue";
import QuizDragComponent from "./components/quiz/QuizDragComponent.vue";
import QuizDropComponent from "./components/quiz/QuizDropComponent.vue";

// Monaco editor
import "monaco-python";

// Vue
const app = createApp({});

app.component("code-runner", CodeRunnerComponent);
app.component("quiz", QuizComponent);
app.component("quiz-choice", QuizChoiceComponent);
app.component("quiz-answer", QuizAnswerComponent);
app.component("quiz-drag-and-drop", QuizDragAndDropComponent);
app.component("quiz-drag", QuizDragComponent);
app.component("quiz-drop", QuizDropComponent);

app.mount("#app");
