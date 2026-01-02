import "./bootstrap";

// EditorJS tools config global variable
import EditorJS from "@editorjs/editorjs";

import Header from "@editorjs/header";
import Delimiter from "@coolbytes/editorjs-delimiter";
import Warning from "@editorjs/warning";
import EditorjsList from "@editorjs/list";
import ImageTool from "@editorjs/image";
import Table from "@editorjs/table";
import Columns from "@calumk/editorjs-columns";

import CodeRunnerTool from "./editorjs/tools/CodeRunnerTool.js";
import CodeBlockTool from "./editorjs/tools/CodeBlockTool.js";
import QuizTool from "./editorjs/tools/QuizTool.js";
import ExerciseTool from "./editorjs/tools/ExerciseTool.js";
import RevisionTool from "./editorjs/tools/RevisionTool.js";
import AccordionTool from "./editorjs/tools/AccordionTool.js";

const columnsTools = {
    header: {
        class: Header,
        inlineToolbar: true,
    },
    delimiter: {
        class: Delimiter,
        config: {
            styleOptions: ["line"],
            defaultStyle: "line",
            lineWidthOptions: [8, 15, 25, 35, 50, 60, 100],
            defaultLineWidth: 60,
            lineThicknessOptions: [1, 2, 3, 4, 5, 6],
            defaultLineThickness: 4,
        },
    },
    warning: Warning,
    list: {
        class: EditorjsList,
        inlineToolbar: true,
    },
    image: {
        class: ImageTool,
        config: {
            endpoints: {
                byFile: "http://127.0.0.1:8000/admin/img/upload",
            },
            additionalRequestHeaders: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        },
    },
    table: {
        class: Table,
        inlineToolbar: true,
        config: {
            rows: 2,
            cols: 3,
            maxRows: 5,
            maxCols: 5,
        },
    },
};
const accordionTools = columnsTools; // Reuse columns tools for accordion nested editor

window.editorJsTools = {
    header: {
        class: Header,
        inlineToolbar: true,
    },
    delimiter: {
        class: Delimiter,
        config: {
            styleOptions: ["line"],
            defaultStyle: "line",
            lineWidthOptions: [8, 15, 25, 35, 50, 60, 100],
            defaultLineWidth: 60,
            lineThicknessOptions: [1, 2, 3, 4, 5, 6],
            defaultLineThickness: 4,
        },
    },
    warning: Warning,
    list: {
        class: EditorjsList,
        inlineToolbar: true,
    },
    image: {
        class: ImageTool,
        config: {
            endpoints: {
                byFile: "http://127.0.0.1:8000/admin/img/upload",
            },
            additionalRequestHeaders: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        },
    },
    table: {
        class: Table,
        inlineToolbar: true,
        config: {
            rows: 2,
            cols: 3,
            maxRows: 5,
            maxCols: 5,
        },
    },
    columns: {
        class: Columns,
        inlineToolbar: true,
        config: {
            EditorJsLibrary: EditorJS,
            tools: columnsTools,
        },
    },

    // Custom
    codeRunner: {
        class: CodeRunnerTool,
        inlineToolbar: true,
    },
    codeBlock: {
        class: CodeBlockTool,
        inlineToolbar: true,
    },
    quiz: {
        class: QuizTool,
        inlineToolbar: true,
    },
    exercise: {
        class: ExerciseTool,
        inlineToolbar: true,
    },
    revision: {
        class: RevisionTool,
        inlineToolbar: true,
    },
    accordion: {
        class: AccordionTool,
        inlineToolbar: true,
        config: {
            tools: accordionTools,
        },
    },
};

// Vue
import { createApp, reactive } from "vue";
import CodeRunnerComponent from "./components/CodeRunnerComponent.vue";
import CodeBlockComponent from "./components/CodeBlockComponent.vue";
import ExerciseComponent from "./components/ExerciseComponent.vue";
import QuizComponent from "./components/QuizComponent.vue";
import QuizChoiceComponent from "./components/quiz/QuizChoiceComponent.vue";
import QuizAnswerComponent from "./components/quiz/QuizAnswerComponent.vue";
import QuizDragAndDropComponent from "./components/quiz/QuizDragAndDropComponent.vue";
import QuizDragComponent from "./components/quiz/QuizDragComponent.vue";
import QuizDropComponent from "./components/quiz/QuizDropComponent.vue";
import AdminEditorComponent from "./components/AdminEditorComponent.vue";
import AlertComponent from "./components/AlertComponent.vue";
import AlertsContainerComponent from "./components/AlertsContainerComponent.vue";

const app = createApp({});

app.config.compilerOptions.whitespace = "preserve";

app.component("code-runner", CodeRunnerComponent);
app.component("code-block", CodeBlockComponent);
app.component("exercise", ExerciseComponent);
app.component("quiz", QuizComponent);
app.component("quiz-choice", QuizChoiceComponent);
app.component("quiz-answer", QuizAnswerComponent);
app.component("quiz-drag-and-drop", QuizDragAndDropComponent);
app.component("quiz-drag", QuizDragComponent);
app.component("quiz-drop", QuizDropComponent);
app.component("admin-editor", AdminEditorComponent);
app.component("alert", AlertComponent);
app.component("alerts-container", AlertsContainerComponent);

const completedQuizzes = reactive(window.completedQuizzes || []);
const completedExercises = reactive(window.completedExercises || []);
app.provide("completedQuizzes", completedQuizzes);
app.provide("completedExercises", completedExercises);

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
