import "./bootstrap";
import { createApp } from "vue";
import CodeRunnerComponent from "./components/CodeRunnerComponent.vue";
import * as monaco from "monaco-editor";
import editorWorker from "monaco-editor/esm/vs/editor/editor.worker?worker";

// Vue
const app = createApp({});

app.component("code-runner", CodeRunnerComponent);

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
