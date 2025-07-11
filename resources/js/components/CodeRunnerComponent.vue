<template>
    <div
        ref="editorContainer"
        class="border flex-grow-1"
        style="width: calc(100% - 14px); height: 500px"
    ></div>
    <button @click="console.log(getEditorText())" class="btn btn-primary">
        Spustiť
    </button>
</template>
<script setup>
import * as monaco from "monaco-editor";
import { onMounted, ref } from "vue";

const props = defineProps({
    id: Number,
});

let editor = null;
const editorContainer = ref(null);

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

function getEditorText() {
    if (editor) {
        return editor.getValue();
    }

    return "";
}
</script>
