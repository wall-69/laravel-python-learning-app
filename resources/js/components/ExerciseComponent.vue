<template>
    <div class="mb-3">
        <h2 v-if="slots.header">
            <slot name="header"></slot>
        </h2>
        <p v-if="slots.description" class="mb-0">
            <slot name="description"></slot>
        </p>
        <p v-if="!exerciseIsComplete">
            Ak úspešne vyriešiš toto cvičenie, tak dostaneš
            <span class="text-success fw-bold">+35 BODOV</span>. Tvoje riešenie
            bude otestované. Pre úspech musí prejsť všetkými testami. Neúspešné
            testy sa zobrazia nižšie. Cvičenie si môžeš zopakovať koľkokrát len
            chceš.
        </p>
        <p v-else class="fst-italic fw-bold">
            Toto cvičenie si už úspešne vyriešil! Nižšie je tvoj kód tvojho
            úspešného riešenia. Ak sa ti chce, tak si ho kľudne môžeš zopakovať.
        </p>
        <p class="border bg-info text-bg-info px-3 py-1 mb-0">
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

        <h3 class="mt-2 mb-0">Konzola</h3>
        <textarea
            ref="editorConsole"
            class="form-control font-monospace rounded-0"
            style="height: 200px; box-shadow: none"
            spellcheck="false"
            readonly
        ></textarea>

        <h3 class="mt-2 mb-0">Kontrola</h3>
        <div v-if="testResults.length > 0" class="d-flex flex-column">
            <div
                v-for="test in testResults"
                class="px-2 py-1 border d-flex flex-column flex-md-row gap-1"
                :class="{
                    'text-bg-success': test.success,
                    'text-bg-danger': !test.success,
                }"
                style="transition: background-color 0.5s"
            >
                <span class="fw-bold d-flex align-items-center">
                    <i
                        class="me-1"
                        :class="{
                            'bx bx-check fw-bold': test.success,
                            'bx bx-x fw-bold': !test.success,
                        }"
                    ></i>
                    {{ test.success ? "Úspešný" : "Neúspešný" }} test:
                </span>
                <p class="mb-0" v-html="test.message"></p>
            </div>
        </div>
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
import { computed, inject, onMounted, ref, useSlots } from "vue";
import { addAlert, normalizeIndentation } from "../helpers";

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
const editorConsole = ref(null);

const loading = ref(false);
const testResults = ref([]);

const completedExercises = inject("completedExercises");

// Computed
const exerciseIsComplete = computed(() =>
    completedExercises.includes(props.id)
);

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
            if (response.data.error) {
                testResults.value = [];
                editorConsole.value.value = response.data.error;
                return;
            }

            if (response.data.celebrate && !exerciseIsComplete.value) {
                addAlert("celebrate", response.data.celebrate);
            }

            testResults.value = response.data.test_results;
            editorConsole.value.value = response.data.user_output;
        }
    } catch (error) {
        // Server responded with a status outside 2xx
        if (error.response) {
            // Not logged in
            if (error.response.status === 401) {
                addAlert(
                    "warning",
                    "Pre používanie spúšťača kódu sa musíte prihlásiť."
                );
                return;
            }

            // Email not verified
            if (error.response.status === 403) {
                addAlert(
                    "warning",
                    "Pre používanie spúšťača kódu musíte overiť svoj email."
                );
                return;
            }

            testResults.value = [];
            editorConsole.value.value = "Error: " + error.response.statusText;
        }

        // No response
        else if (error.request) {
            testResults.value = [];
            editorConsole.value.value =
                "Error: Server neodpovedal. Skúste znova alebo neskôr.";
        }

        // Other errors
        else {
            testResults.value = [];
            editorConsole.value.value = "Error: " + error.message;
        }
    } finally {
        loading.value = false;
    }

    if (
        testResults.value.every((test) => test.success) &&
        !exerciseIsComplete.value
    ) {
        try {
            const response = await axios.post(
                "/user/progress/complete/exercise/" + props.id,
                { code: getEditorText() }
            );

            if (response.status === 200) {
                completedExercises.push(props.id);

                if (response.data.level_up) {
                    addAlert("level-up", response.data.level_up);
                }
            }
        } catch (error) {
            console.error("Marking exercise as complete failed: " + error);
        }
    }
}

function getEditorText() {
    if (editor) {
        return editor.getValue();
    }

    return "";
}
</script>
