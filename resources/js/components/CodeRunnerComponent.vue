<template>
    <div>
        <h2 v-if="slots.header">
            <slot name="header"></slot>
        </h2>
        <p v-if="slots.description">
            <slot name="description"></slot>
        </p>

        <div
            ref="editorContainer"
            class="border"
            style="width: calc(100% - 14px); height: 300px"
        ></div>
        <button @click="runCode" class="mt-1 btn btn-primary">Spustiť</button>

        <h3 class="mt-3">Výstup</h3>
        <textarea
            ref="editorOutput"
            class="form-control"
            style="width: calc(100% - 14px); height: 200px"
            readonly
        ></textarea>

        <!-- Value slot -->
        <pre
            ref="editorValue"
            class="d-none"
            style="white-space: pre"
        ><slot name="code"></slot></pre>
    </div>
</template>
<script setup>
import axios from "axios";
import * as monaco from "monaco-editor";
import { onMounted, ref, useSlots } from "vue";

// Composables
const slots = useSlots();

// Lifecycle
onMounted(() => {
    if (editorContainer.value) {
        editor = monaco.editor.create(editorContainer.value, {
            language: "python",
            automaticLayout: true,
            scrollBeyondLastLine: false,
            minimap: { enabled: false },
        });

        editor.setValue(normalizeIndentation(editorValue.value.innerText));
    }
});

// Variables
let editor = null;
const editorContainer = ref(null);
const editorOutput = ref(null);
const editorValue = ref(null);

// Functions
async function runCode() {
    const code = getEditorText();

    try {
        const response = await axios.post("/code-runner", { code });

        if (response.status === 200) {
            editorOutput.value.value = response.data.output;
        }
    } catch (error) {
        // Server responded with a status outside 2xx
        if (error.response) {
            // Not logged in
            if (error.response.status === 401) {
                window.location.href = "/login";
                return;
            }

            // Email not verified
            if (error.response.status === 403) {
                window.location.href = "/email/verify";
                return;
            }

            editorOutput.value.value =
                "Error: " + error.response.data.message ||
                error.response.statusText;
        }

        // No response
        else if (error.request) {
            editorOutput.value.value =
                "Error: Server neodpovedal. Skúste znova alebo neskôr.";
        }

        // Other errors
        else {
            editorOutput.value.value = "Error: " + error.message;
        }
    }
}

function getEditorText() {
    if (editor) {
        return editor.getValue();
    }

    return "";
}

function normalizeIndentation(text) {
    const lines = text.split("\n");

    // Remove empty leading and trailing lines
    while (lines.length && lines[0].trim() === "") lines.shift();
    while (lines.length && lines[lines.length - 1].trim() === "") lines.pop();

    // Find minimum indentation
    const indentLengths = lines
        .filter((line) => line.trim() !== "")
        .map((line) => line.match(/^ */)[0].length);
    const minIndent = Math.min(...indentLengths);

    // Remove common indentation
    return lines.map((line) => line.slice(minIndent)).join("\n");
}
</script>
