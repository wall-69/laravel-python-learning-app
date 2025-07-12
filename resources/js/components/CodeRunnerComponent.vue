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

const props = defineProps({
    id: Number,
});

let editor = null;
const editorContainer = ref(null);
const editorOutput = ref(null);

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

async function runCode() {
    const code = getEditorText();

    const response = await axios.post("/code-runner", {
        code: code,
    });

    if (response.status == 200) {
        editorOutput.value.value = response.data.output;
    }
}

function getEditorText() {
    if (editor) {
        return editor.getValue();
    }

    return "";
}
</script>
