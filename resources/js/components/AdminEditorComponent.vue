<template>
    <div>
        <div id="editor" class="pt-3 border"></div>

        <button @click="saveLecture" class="mt-5 btn btn-primary">
            Uložiť
        </button>
        <pre id="testOutput" style="white-space: pre"></pre>
    </div>
</template>
<script setup>
import useEditor from "../editorjs/editor-init";
import { onMounted, ref } from "vue";

// Lifecycle
onMounted(() => {
    editor.value = useEditor("editor");
});

// Variables
const editor = ref(null);

// Functions
async function saveLecture() {
    try {
        const output = await editor.value.save();
        document.getElementById("testOutput").textContent = JSON.stringify(
            output,
            null,
            2
        );
    } catch (error) {
        console.error("Save failed: ", error);
    }
}
</script>
