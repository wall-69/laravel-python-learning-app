<template>
    <div>
        <div id="editor" class="pt-3 border"></div>
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
</script>
