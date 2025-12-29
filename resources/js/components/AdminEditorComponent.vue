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
import Header from "@editorjs/header";
import Delimiter from "@coolbytes/editorjs-delimiter";
import Warning from "@editorjs/warning";
import EditorjsList from "@editorjs/list";
import ImageTool from "@editorjs/image";
import Table from "@editorjs/table";
import Columns from "@calumk/editorjs-columns";

import CodeRunnerTool from "../editorjs/tools/CodeRunnerTool.js";
import CodeBlockTool from "../editorjs/tools/CodeBlockTool.js";
import QuizTool from "../editorjs/tools/QuizTool.js";
import ExerciseTool from "..//editorjs/tools/ExerciseTool.js";
import RevisionTool from "../editorjs/tools/RevisionTool.js";
import FAQTool from "../editorjs/tools/FAQTool.js";

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
    const columnsTools = {
        header: {
            class: Header,
            inlineToolbar: true,
        },
        delimiter: {
            class: Delimiter,
            config: {
                styleOptions: ["line"],
                defaultStyle: "line",
                lineWidthOptions: [8, 15, 25, 35, 50, 60, 100],
                defaultLineWidth: 60,
                lineThicknessOptions: [1, 2, 3, 4, 5, 6],
                defaultLineThickness: 4,
            },
        },
        warning: Warning,
        list: {
            class: EditorjsList,
            inlineToolbar: true,
        },
        image: {
            class: ImageTool,
            config: {
                endpoints: {
                    byFile: "http://127.0.0.1:8000/admin/img/upload",
                },
                additionalRequestHeaders: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            },
        },
        table: {
            class: Table,
            inlineToolbar: true,
            config: {
                rows: 2,
                cols: 3,
                maxRows: 5,
                maxCols: 5,
            },
        },
    };

    editor = new EditorJS({
        holder: "editor",
        autofocus: true,
        placeholder: "Finally, Consider Moving on If All Else Fails.",
        logLevel: "WARN", // PROD: set to ERROR

        tools: {
            header: {
                class: Header,
                inlineToolbar: true,
            },
            delimiter: {
                class: Delimiter,
                config: {
                    styleOptions: ["line"],
                    defaultStyle: "line",
                    lineWidthOptions: [8, 15, 25, 35, 50, 60, 100],
                    defaultLineWidth: 60,
                    lineThicknessOptions: [1, 2, 3, 4, 5, 6],
                    defaultLineThickness: 4,
                },
            },
            warning: Warning,
            list: {
                class: EditorjsList,
                inlineToolbar: true,
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: "http://127.0.0.1:8000/admin/img/upload",
                    },
                    additionalRequestHeaders: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                },
            },
            table: {
                class: Table,
                inlineToolbar: true,
                config: {
                    rows: 2,
                    cols: 3,
                    maxRows: 5,
                    maxCols: 5,
                },
            },
            columns: {
                class: Columns,
                inlineToolbar: true,
                config: {
                    EditorJsLibrary: EditorJS,
                    tools: columnsTools,
                },
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
            exercise: {
                class: ExerciseTool,
                inlineToolbar: true,
            },
            revision: {
                class: RevisionTool,
                inlineToolbar: true,
            },
            faq: {
                class: FAQTool,
                inlineToolbar: true,
            },
        },

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
