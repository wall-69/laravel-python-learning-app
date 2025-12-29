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
        <h3 class="mt-3">Konzola</h3>
        <textarea
            ref="editorConsole"
            @keydown="onConsoleKeyDown"
            @click="onConsoleClick"
            @select="onConsoleSelect"
            @paste.prevent
            @cut.prevent
            class="form-control font-monospace rounded-0"
            style="height: 200px; box-shadow: none"
            spellcheck="false"
            readonly
        ></textarea>

        <div ref="editorValue" class="d-none" style="white-space: pre">
            <slot name="code"></slot>
        </div>
    </div>
</template>

<script setup>
import { io } from "socket.io-client";
import * as monaco from "monaco-editor";
import { onMounted, ref, useSlots, nextTick } from "vue";
import { addAlert, normalizeIndentation } from "../helpers";

const slots = useSlots();

onMounted(() => {
    if (editorContainer.value) {
        editorOptions = {
            language: "python",
            automaticLayout: true,
            scrollBeyondLastLine: false,
            minimap: { enabled: false },
        };

        editor = monaco.editor.create(editorContainer.value, editorOptions);
        editor.setValue(normalizeIndentation(editorValue.value.innerText));
    }
});

let editor = null;
let editorOptions = {};
let socket = null;
const editorContainer = ref(null);
const editorConsole = ref(null);
const editorValue = ref(null);
const loading = ref(false);

// Buffered output to reduce frequent DOM writes
let outputBuffer = "";
let flushTimer = null;
const FLUSH_INTERVAL_MS = 50;

// Console input
const waitingForInput = ref(false);
const inputValue = ref("");
let lockedLength = 0;

async function runCode() {
    if (loading.value) {
        return;
    }

    const code = getEditorText();
    if (!code) {
        return;
    }

    editorConsole.value.value = "";
    inputValue.value = "";
    waitingForInput.value = false;
    loading.value = true;

    socket = io("http://127.0.0.1:3000", {
        reconnection: false,
        withCredentials: true,
    });

    socket.emit("run", { code: code });

    socket.on("output", (output) => {
        addConsoleOutput(output);
    });

    socket.on("waiting_for_input", (waiting) => {
        waitingForInput.value = true;

        // Mark current output as locked (immutable) and make console writable
        lockedLength = editorConsole.value
            ? editorConsole.value.value.length
            : 0;
        editorConsole.value.readOnly = false;

        nextTick(() => {
            if (editorConsole.value) {
                editorConsole.value.focus();
            }
        });
    });

    socket.on("error", (error) => {
        addConsoleOutput("\nNastala chyba:\n" + error);
        waitingForInput.value = false;
        editorConsole.value.readOnly = true;
    });

    socket.on("connect_error", (error) => {
        loading.value = false;
        waitingForInput.value = false;
        socket.disconnect();

        switch (error.message) {
            case "not_authenticated":
                addAlert(
                    "warning",
                    "Pre používanie spúšťača kódu sa musíte prihlásiť."
                );
                break;

            case "auth_error":
                addAlert(
                    "danger",
                    "Nepodarilo sa overiť, či ste prihlásení. Prosím, obnovte stránku a skúste to znova alebo nás kontaktujte."
                );
                break;
            default:
                addAlert(
                    "danger",
                    "Nastala chyba pri spúšťaní kódu, skúste to znova alebo nás kontaktujte."
                );
                break;
        }
    });

    socket.on("disconnect", () => {
        // Flush any remaining buffered output and finish
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
    socket.emit("input", inputValue.value + "\n");

    // Append the entered line (and newline) to the console and make it immutable
    if (editorConsole.value) {
        editorConsole.value.value = editorConsole.value.value.substr(
            0,
            lockedLength
        );
        lockedLength = editorConsole.value.value.length;
    }

    inputValue.value = "";
    waitingForInput.value = false;

    editorConsole.value.readOnly = true;
}

function getEditorText() {
    if (editor) {
        return editor.getValue();
    }

    return "";
}

function addConsoleOutput(output) {
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
    if (!editorConsole.value) {
        outputBuffer = "";
        return;
    }

    if (outputBuffer.length == 0) return;

    editorConsole.value.value += outputBuffer;

    editorConsole.value.scrollTop = editorConsole.value.scrollHeight;

    // Update lockedLength to reflect immutable printed output
    lockedLength = editorConsole.value.value.length;

    outputBuffer = "";
}

function onConsoleKeyDown(e) {
    if (!waitingForInput.value) {
        return;
    }
    const start = editorConsole.value.selectionStart;
    const end = editorConsole.value.selectionEnd;
    const hasSelection = start !== end;

    // Handle Ctrl/Cmd + A to select only editable area
    if ((e.ctrlKey || e.metaKey) && e.key === "a") {
        e.preventDefault();
        editorConsole.value.setSelectionRange(
            lockedLength,
            editorConsole.value.value.length
        );
        return;
    }

    // Block backspace before locked area
    if (e.key === "Backspace") {
        if (hasSelection) {
            if (end <= lockedLength || start < lockedLength) {
                e.preventDefault();
            }
        } else {
            if (start <= lockedLength) {
                e.preventDefault();
            }
        }
    }

    // Block delete before locked area
    if (e.key === "Delete" && start < lockedLength) {
        e.preventDefault();
    }

    // Block left arrow before locked area
    if (e.key === "ArrowLeft" && start <= lockedLength) {
        e.preventDefault();
    }

    // Handle Enter key to send input
    if (e.key === "Enter") {
        e.preventDefault();

        sendInput();

        if (editorConsole.value) {
            editorConsole.value.readOnly = true;
        }
    }

    requestAnimationFrame(() => {
        const full = editorConsole.value.value;
        inputValue.value = full.slice(lockedLength);
    });
}

function onConsoleClick() {
    if (!waitingForInput.value) {
        return;
    }

    // Move cursor to the end if clicked before locked area
    if (editorConsole.value.selectionStart < lockedLength) {
        const end = editorConsole.value.value.length;
        editorConsole.value.selectionStart = end;
        editorConsole.value.selectionEnd = end;
    }
}

function onConsoleSelect() {
    if (!waitingForInput.value) {
        return;
    }

    const start = editorConsole.value.selectionStart;
    const end = editorConsole.value.selectionEnd;

    // If selection extends into locked area, constrain it
    if (start < lockedLength || end < lockedLength) {
        requestAnimationFrame(() => {
            editorConsole.value.setSelectionRange(
                Math.max(lockedLength, start),
                Math.max(lockedLength, end)
            );
        });
    }
}
</script>
