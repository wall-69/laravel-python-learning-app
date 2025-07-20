<template>
    <div
        ref="editorContainer"
        class="border flex-grow-1"
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
</template>
<script setup>
import axios from "axios";
import * as monaco from "monaco-editor";
import { onMounted, ref } from "vue";

// Define
const props = defineProps({
    id: Number,
});

// Lifecycle
onMounted(() => {
    if (editorContainer.value) {
        editor = monaco.editor.create(editorContainer.value, {
            value: 'print("Ahoj, svet! Moje ID je ' + props.id + '")',
            language: "python",
            automaticLayout: true,
            minimap: { enabled: false },
        });
    }
});

// Variables
let editor = null;
const editorContainer = ref(null);
const editorOutput = ref(null);

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
</script>
