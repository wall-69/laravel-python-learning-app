<template>
    <div class="d-flex justify-content-end">
        <div
            ref="drag"
            class="position-absolute border drag z-1 bg-white px-3 py-2 text-center"
            style="width: 240px"
            :data-drop="drop"
            :data-in-drop="inDrop"
        >
            <slot></slot>
        </div>
    </div>
</template>
<script setup>
import { onMounted, onUnmounted, ref } from "vue";

const props = defineProps({
    drop: Number,
});

const drag = ref(null);
const inDrop = ref(-1);

let startX = 0,
    startY = 0;
let offsetX = 0,
    offsetY = 0;
let dragging = false;

onMounted(() => {
    startX = drag.value.offsetLeft;
    startY = drag.value.offsetTop;

    drag.value.addEventListener("mousedown", onMouseDown);
});

onUnmounted(() => {
    document.removeEventListener("mousemove", onMouseMove);
    document.removeEventListener("mouseup", onMouseUp);
});

function onMouseDown(e) {
    dragging = true;
    offsetX = e.clientX - drag.value.offsetLeft;
    offsetY = e.clientY - drag.value.offsetTop;
    e.preventDefault();

    document.addEventListener("mousemove", onMouseMove);
    document.addEventListener("mouseup", onMouseUp);
}

function onMouseMove(e) {
    if (!dragging) return;
    drag.value.style.left = `${e.clientX - offsetX}px`;
    drag.value.style.top = `${e.clientY - offsetY}px`;
}

function onMouseUp(e) {
    if (!dragging) return;
    dragging = false;

    document.removeEventListener("mousemove", onMouseMove);
    document.removeEventListener("mouseup", onMouseUp);

    // Hide dragged element to detect underlying element
    drag.value.style.pointerEvents = "none";
    const droppedOn = document.elementFromPoint(e.clientX, e.clientY);
    drag.value.style.pointerEvents = "";

    if (!droppedOn || !droppedOn.classList.contains("drop")) {
        drag.value.style.left = `${startX}px`;
        drag.value.style.top = `${startY}px`;

        inDrop.value = -1;
        return;
    }

    // Move the drag to the center of drop
    const rect = droppedOn.getBoundingClientRect();
    const elRect = drag.value.getBoundingClientRect();
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    // Center
    drag.value.style.left = `${
        rect.left + rect.width / 2 - elRect.width / 2 - parentRect.left
    }px`;
    drag.value.style.top = `${
        rect.top + rect.height / 2 - elRect.height / 2 - parentRect.top
    }px`;

    inDrop.value = droppedOn.dataset.id;
}
</script>
<style>
.drag {
    cursor: grab;
    transition: left 0.05s, top 0.05s;

    /* Disable text selection */
    user-select: none;
    -webkit-user-drag: none;
}

.drag:active {
    cursor: grabbing;
}
</style>
