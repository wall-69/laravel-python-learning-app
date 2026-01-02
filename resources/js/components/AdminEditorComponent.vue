<template>
    <div>
        <div
            v-if="isScreenWide"
            id="editor"
            class="admin-editor pt-3 border"
        ></div>
        <p v-else class="text-warning fw-bold">
            Editor lekcií nie je podporovaný na vašom rozlíšení.
        </p>
    </div>
</template>
<script setup>
import EditorJS from "@editorjs/editorjs";

import { computed, onBeforeUnmount, onMounted, ref } from "vue";

// Lifecycle
onMounted(async () => {
    window.addEventListener("resize", onResize);

    if (isScreenWide.value) {
        initEditor();
    }
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", onResize);
});

// Variables
const screenWidth = ref(window.innerWidth);

// Computed
const isScreenWide = computed(() => screenWidth.value > 992);
let editor = null;

// Functions
function onResize(e) {
    screenWidth.value = window.innerWidth;

    if (isScreenWide.value && !editor) {
        initEditor();
    } else if (!isScreenWide.value && editor) {
        editor.clear();
        editor.destroy();

        editor = null;
    }
}

function initEditor() {
    editor = new EditorJS({
        holder: "editor",
        autofocus: true,
        placeholder: "Finally, Consider Moving on If All Else Fails.",
        logLevel: "WARN", // PROD: set to ERROR

        tools: window.editorJsTools,

        onReady: () => {
            const input = document.querySelector("#blocksInput");
            if (!input.value || !editor) {
                return;
            }

            editor.render(JSON.parse(input.value));
        },
        onChange: async () => {
            if (!editor) {
                return;
            }

            const saved = await editor.save();
            document.querySelector("#blocksInput").value =
                JSON.stringify(saved);
        },

        data: {},
    });
}
</script>
