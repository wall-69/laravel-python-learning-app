<template>
    <div>
        <div id="editor" class="pt-3 border" style="min-width: 900px"></div>
    </div>
</template>
<script setup>
import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";

import CodeRunnerTool from "../editorjs/tools/CodeRunnerTool.js";
import CodeBlockTool from "../editorjs/tools/CodeBlockTool.js";
import QuizTool from "../editorjs/tools/QuizTool.js";

import { onMounted } from "vue";

// Lifecycle
onMounted(async () => {
    const editor = new EditorJS({
        holder: "editor",
        autofocus: true,
        placeholder: "Finally, Consider Moving on If All Else Fails.",
        logLevel: "WARN", // TODO: set to ERROR in prod

        tools: {
            header: {
                class: Header,
                inlineToolbar: true,
            },

            // Custom
            codeRunner: {
                class: CodeRunnerTool,
                inlineToolbar: true,
            },
            codeBlock: {
                class: CodeBlockTool,
                inlineToolbar: true,
            },
            quiz: {
                class: QuizTool,
                inlineToolbar: true,
            },
        },

        onReady: () => {
            const input = document.querySelector("#blocksInput");
            if (!input.value) {
                return;
            }

            editor.render(JSON.parse(input.value));
        },
        onChange: async () => {
            const saved = await editor.save();
            document.querySelector("#blocksInput").value =
                JSON.stringify(saved);
        },

        data: {},
    });
});
</script>
