<template>
    <div class="mb-3">
        <h2 v-if="slots.header">
            <slot name="header"></slot>
        </h2>
        <p v-if="slots.description">
            <slot name="description"></slot>
        </p>

        <div
            ref="codeBlockContainer"
            class="border"
            :style="{ height: heightBasedOnContent, overflow: 'hidden' }"
        ></div>

        <!-- Value slot -->
        <div ref="codeBlockValue" class="d-none" style="white-space: pre">
            <slot name="code"></slot>
        </div>
    </div>
</template>
<script setup>
import * as monaco from "monaco-editor";
import { onMounted, ref, useSlots } from "vue";
import { normalizeIndentation } from "../helpers";

// Composables
const slots = useSlots();

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

        // Initial height adjustment
        const lineHeight = codeBlockEditor.getOption(
            monaco.editor.EditorOption.lineHeight
        );
        const lineCount = codeBlockEditor.getModel().getLineCount();

        heightBasedOnContent.value = `${lineHeight * lineCount + 16}px`;
    }
});

// Variables
let codeBlockEditor = null;
const codeBlockContainer = ref(null);
const codeBlockValue = ref(null);
const heightBasedOnContent = ref("auto");
</script>
