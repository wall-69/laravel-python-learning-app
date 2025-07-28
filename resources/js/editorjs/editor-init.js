import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";

import CodeRunnerTool from "./tools/CodeRunnerTool.js";
import CodeBlockTool from "./tools/CodeBlockTool.js";
import QuizTool from "./tools/QuizTool.js";

export default function useEditor(holderId, data = {}) {
    return new EditorJS({
        holder: holderId,
        autofocus: true,
        placeholder: "Finally, Consider Moving on If All Else Fails.",
        logLevel: "WARN", // TODO: set to ERROR in prod

        tools: {
            header: {
                class: Header,
                inlineToolbar: true,
                config: {
                    defaultLevel: 1,
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
        },

        data: data,
    });
}
