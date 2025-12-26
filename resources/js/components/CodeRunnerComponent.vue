<template>
    <div class="mb-3">
        <h2 v-if="slots.header">
            <slot name="header"></slot>
        </h2>
        <p v-if="slots.description">
            <slot name="description"></slot>
        </p>
        <div ref="editorContainer" class="border" style="height: 300px"></div>
        <button
            @click="runCode"
            class="mt-1 btn btn-primary"
            :disabled="loading"
        >
            Spustiť
            <i v-show="loading" class="spinner-border spinner-border-sm"></i>
        </button>
        <h3 class="mt-3">Výstup</h3>
        <textarea
            ref="editorOutput"
            class="form-control"
            style="height: 200px"
            readonly
        ></textarea>

        <!-- Input field for stdin -->
        <div v-if="waitingForInput" class="mt-2">
            <input
                ref="stdinInput"
                v-model="inputValue"
                @keydown.enter="sendInput"
                type="text"
                class="form-control"
                placeholder="Zadajte vstup a stlačte Enter..."
            />
            <button @click="sendInput" class="btn btn-secondary btn-sm mt-1">
                Odoslať
            </button>
        </div>

        <div ref="editorValue" class="d-none" style="white-space: pre">
            <slot name="code"></slot>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { io } from "socket.io-client";
import * as monaco from "monaco-editor";
import { onMounted, ref, useSlots, nextTick } from "vue";
import { normalizeIndentation } from "../helpers";

const slots = useSlots();

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

let editor = null;
let socket = null;
const editorContainer = ref(null);
const editorOutput = ref(null);
const editorValue = ref(null);
const stdinInput = ref(null);
const loading = ref(false);
const waitingForInput = ref(false);
const inputValue = ref("");

// Buffered output to reduce frequent DOM writes
let outputBuffer = "";
let flushTimer = null;
const FLUSH_INTERVAL_MS = 50;

async function runCode() {
    if (loading.value) {
        return;
    }
    const code = getEditorText();
    if (!code) {
        return;
    }

    editorOutput.value.value = "";
    inputValue.value = "";
    waitingForInput.value = false;
    loading.value = true;

    socket = io("http://localhost:3000", { reconnection: false });

    socket.emit("run", { code: code });

    socket.on("output", (output) => {
        addEditorOutput(output);
    });

    // Server tells us when it's waiting for input
    socket.on("waiting_for_input", (waiting) => {
        waitingForInput.value = true;
        nextTick(() => {
            if (stdinInput.value) {
                stdinInput.value.focus();
            }
        });
    });

    socket.on("error", (error) => {
        addEditorOutput("\nNastala chyba:\n" + error);
        waitingForInput.value = false;
    });

    socket.on("disconnect", () => {
        // flush any remaining buffered output and finish
        flushOutput();

        loading.value = false;
        waitingForInput.value = false;
        socket = null;
    });
}

function sendInput() {
    if (!waitingForInput.value || !socket) {
        return;
    }

    // Send to server with newline
    socket.emit("stdin", inputValue.value + "\n");

    // Clear input
    inputValue.value = "";
    waitingForInput.value = false;
}

function getEditorText() {
    if (editor) {
        return editor.getValue();
    }

    return "";
}

function addEditorOutput(output) {
    // Buffer output and schedule a flush to minimize expensive DOM writes
    outputBuffer += output;

    if (!flushTimer) {
        flushTimer = setTimeout(() => {
            flushTimer = null;
            flushOutput();
        }, FLUSH_INTERVAL_MS);
    }
}

function flushOutput() {
    if (!editorOutput.value) {
        outputBuffer = "";
        return;
    }

    if (outputBuffer.length == 0) return;

    editorOutput.value.value += outputBuffer;

    editorOutput.value.scrollTop = editorOutput.value.scrollHeight;

    outputBuffer = "";
}
</script>
