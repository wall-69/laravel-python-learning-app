import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import monacoEditorEsmPlugin from "vite-plugin-monaco-editor-esm";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/sass/app.scss", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
        monacoEditorEsmPlugin({
            languageWorkers: ["editorWorkerService"],
        }),

        // Monaco editor Vite-Laravel proxy
        (() => {
            return {
                name: "monaco-editor:rewrite-worker",
                transform(code, id) {
                    if (this.environment.mode !== "dev") {
                        return;
                    }

                    if (id.includes("worker")) {
                        return code.replace(
                            "__laravel_vite_placeholder__.test",
                            "127.0.0.1:8000"
                        );
                    }
                },
            };
        })(),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },
});
