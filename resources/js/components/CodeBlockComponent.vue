<template>
    <div>
        <h2>
            <slot name="header"></slot>
        </h2>
        <p>
            <slot name="description"></slot>
        </p>

        <div
            ref="codeBlockContainer"
            class="border flex-grow-1"
            style="width: calc(100% - 14px); height: 300px"
        ></div>

        <pre
            ref="codeBlockValue"
            class="d-none"
            style="white-space: pre"
        ><slot name="code"></slot></pre>
    </div>
</template>
<script setup>
import axios from "axios";
import * as monaco from "monaco-editor";
import { onMounted, ref } from "vue";

// Lifecycle
onMounted(() => {
    if (codeBlockContainer.value) {
        codeBlockEditor = monaco.editor.create(codeBlockContainer.value, {
            language: "python",
            automaticLayout: true,
            scrollBeyondLastLine: false,
            minimap: { enabled: false },
            readOnly: true,
        });

        codeBlockEditor.setValue(
            normalizeIndentation(codeBlockValue.value.innerText)
        );
    }
});

// Variables
let codeBlockEditor = null;
const codeBlockContainer = ref(null);
const codeBlockValue = ref(null);

// Functions
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
