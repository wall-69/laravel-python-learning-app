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
import { onMounted, ref } from "vue";
import start, { E_EDITOR_THEME } from "monaco-python";

// Define
const props = defineProps({
    id: Number,
});

// Lifecycle
onMounted(async () => {
    if (editorContainer.value) {
        editor = await start(editorContainer.value, {
            theme: E_EDITOR_THEME.LIGHT_VS,
            value: 'print("Ahoj, svet! Moje ID je ' + props.id + '")',
        }).then((editor) => editor.getMonacoEditorApp().getEditor());
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
</script>
