import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";

import CodeRunnerBlock from "./tools/CodeRunnerBlock.js";

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
            codeRunner: CodeRunnerBlock,
        },

        data: data,
    });
}
