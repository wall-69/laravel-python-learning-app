<template>
    <div>
        <h2 v-if="slots.header">
            <slot name="header"></slot>
        </h2>
        <p v-if="slots.description">
            <slot name="description"></slot>
        </p>
        <p class="border bg-info text-bg-info px-3 py-1">
            <span class="fw-bold">ZADANIE</span>
            <br />

            <slot name="assignment"></slot>
        </p>
        <p>
            Ak úspešne vyriešiš toto cvičenie, tak dostaneš
            <span class="text-success fw-bold">+35 BODOV</span>. Cvičenie si
            môžeš zopakovať koľkokrát len chceš.
        </p>

        <div ref="editorContainer" class="border" style="height: 300px"></div>
        <button @click="runCode" class="mt-1 btn btn-primary">Odovzdať</button>

        <!-- Value slot -->
        <div ref="editorValue" class="d-none" style="white-space: pre">
            <slot name="code"></slot>
        </div>

        <!-- Tests slot -->
        <div class="d-none">
            <slot name="tests"></slot>
        </div>
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
const editorValue = ref(null);

// Functions
async function runCode() {
    const code = getEditorText();

    try {
        const response = await axios.post("/exercise", { code });

        if (response.status === 200) {
            // TODO: handle OK response
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

            // TODO: handle error response
        }

        // No response
        else if (error.request) {
            // TODO: handle no response
        }

        // Other errors
        else {
            // TODO: handle other errors
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
