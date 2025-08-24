<template>
    <div>
        <h2 v-if="slots.header">
            <slot name="header"></slot>
        </h2>
        <p v-if="slots.description">
            <slot name="description"></slot>
        </p>
        <p>
            Ak úspešne vyriešiš toto cvičenie, tak dostaneš
            <span class="text-success fw-bold">+35 BODOV</span>. Tvoje riešenie
            bude otestované. Pre úspech musí prejsť všetkými testami. Neúspešné
            testy sa zobrazia nižšie. Cvičenie si môžeš zopakovať koľkokrát len
            chceš.
        </p>
        <p class="border bg-info text-bg-info px-3 py-1">
            <span class="fw-bold">ZADANIE</span>
            <br />

            <slot name="assignment"></slot>
        </p>

        <h3 class="mt-2 mb-0">Riešenie</h3>
        <div ref="editorContainer" class="border" style="height: 300px"></div>
        <button
            @click="runCode"
            class="mt-1 btn btn-primary"
            :disabled="loading"
        >
            Odovzdať
            <i v-show="loading" class="spinner-border spinner-border-sm"></i>
        </button>

        <h3 class="mt-2 mb-0">Kontrola</h3>
        <div v-if="testResults.length > 0 && !error" class="d-flex flex-column">
            <p
                v-for="test in testResults"
                class="mb-0 px-2 py-1 border"
                :class="{
                    'text-bg-success': test.success,
                    'text-bg-danger': !test.success,
                }"
            >
                <span class="fw-bold"
                    >{{ test.success ? "Úspešný" : "Neúspešný" }} test:</span
                >
                {{ test.message }}
            </p>
        </div>
        <textarea
            v-else-if="error"
            class="form-control"
            style="height: 200px"
            readonly
            :value="error"
        ></textarea>
        <p v-else class="mb-0">
            Keď svoje riešenie odovzdáš, tak bude skontrolované, či je správne.
        </p>

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
import { computed, onMounted, ref, useSlots } from "vue";

// Define
const props = defineProps({
    id: String,
});

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
const testResults = ref([]);
const error = ref("");
const loading = ref(false);

// Functions
async function runCode() {
    if (loading.value) {
        return;
    }

    const code = getEditorText();
    if (!code) {
        return;
    }

    loading.value = true;

    try {
        const response = await axios.post("/exercise/" + props.id + "/submit", {
            code,
        });

        if (response.status === 200) {
            console.log(response.data);

            if (response.data.error) {
                error.value = response.data.error;
                testResults.value = [];
                return;
            }

            error.value = "";
            testResults.value = response.data.test_results;
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
    } finally {
        loading.value = false;
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
