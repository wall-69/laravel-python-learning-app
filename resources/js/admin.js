// EditorJS tools config global variable
import EditorJS from "@editorjs/editorjs";

import Header from "@editorjs/header";
import Delimiter from "@coolbytes/editorjs-delimiter";
import Warning from "@editorjs/warning";
import EditorjsList from "@editorjs/list";
import ImageTool from "@editorjs/image";
import Table from "@editorjs/table";
import Columns from "@calumk/editorjs-columns";

import CodeRunnerTool from "./editorjs/tools/CodeRunnerTool.js";
import CodeBlockTool from "./editorjs/tools/CodeBlockTool.js";
import QuizTool from "./editorjs/tools/QuizTool.js";
import ExerciseTool from "./editorjs/tools/ExerciseTool.js";
import RevisionTool from "./editorjs/tools/RevisionTool.js";
import AccordionTool from "./editorjs/tools/AccordionTool.js";

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
                    ?.getAttribute("content"),
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
const accordionTools = columnsTools; // Reuse columns tools for accordion nested editor

window.editorJsTools = {
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
                    ?.getAttribute("content"),
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
    accordion: {
        class: AccordionTool,
        inlineToolbar: true,
        config: {
            tools: accordionTools,
        },
    },
};

document.addEventListener("DOMContentLoaded", () => {
    const updateButton = document.querySelector(".editor-update-blocks-button");

    if (updateButton) {
        updateButton.addEventListener("click", async (e) => {
            e.preventDefault();

            const blocksInput = document.querySelector("#blocksInput");
            if (!blocksInput) {
                return;
            }

            const lectureId = updateButton.getAttribute("data-lecture-id");

            try {
                updateButton.classList.add("disabled");

                const response = await axios.patch(
                    `/admin/lectures/${lectureId}/blocks`,
                    { blocks: blocksInput.value }
                );

                if (response?.data?.success) {
                    updateButton.classList.remove("disabled");
                } else {
                    updateButton.classList.add("btn-danger");
                    console.error("Update blocks failed:", response?.data);
                }
            } catch (error) {
                updateButton.classList.add("btn-danger");
                console.error("Update blocks error:", error.response || error);
            }
        });
    }
});
