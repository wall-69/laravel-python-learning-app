<template>
    <div class="position-relative">
        <div
            ref="drag"
            class="position-absolute border drag z-1 bg-white px-3 py-2 text-center"
            :class="{
                'overflow-y-scroll': !dragging,
                'overflow-y-hidden': dragging,
                'z-2': dragging,
            }"
            style="width: 285.6px; max-height: 150px"
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
const dragging = ref(false);
let relX = 0;
let relY = 0;

onMounted(() => {
    startX = drag.value.offsetLeft;
    startY = drag.value.offsetTop;

    const parentRect = drag.value.offsetParent.getBoundingClientRect();
    relX = startX / parentRect.width;
    relY = startY / parentRect.height;

    drag.value.addEventListener("mousedown", onMouseDown);
    drag.value.addEventListener("touchstart", onTouchStart, { passive: false });

    window.addEventListener("resize", onWindowResize);
});

onUnmounted(() => {
    document.removeEventListener("mousemove", onMouseMove);
    document.removeEventListener("mouseup", onMouseUp);

    window.removeEventListener("resize", onWindowResize);
});

function onMouseDown(e) {
    dragging.value = true;
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    offsetX =
        e.pageX -
        (parentRect.left + window.pageXOffset) -
        drag.value.offsetLeft;
    offsetY =
        e.pageY - (parentRect.top + window.pageYOffset) - drag.value.offsetTop;
    e.preventDefault();

    document.addEventListener("mousemove", onMouseMove);
    document.addEventListener("mouseup", onMouseUp);
}

function onMouseMove(e) {
    if (!dragging.value) return;
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    drag.value.style.left = `${
        e.pageX - (parentRect.left + window.pageXOffset) - offsetX
    }px`;
    drag.value.style.top = `${
        e.pageY - (parentRect.top + window.pageYOffset) - offsetY
    }px`;
}

function onMouseUp(e) {
    if (!dragging.value) return;
    dragging.value = false;

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

function onTouchStart(e) {
    if (e.touches.length !== 1) return;
    dragging.value = true;

    const touch = e.touches[0];
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    offsetX =
        touch.pageX -
        (parentRect.left + window.pageXOffset) -
        drag.value.offsetLeft;
    offsetY =
        touch.pageY -
        (parentRect.top + window.pageYOffset) -
        drag.value.offsetTop;

    document.addEventListener("touchmove", onTouchMove, { passive: false });
    document.addEventListener("touchend", onTouchEnd);
    e.preventDefault();
}

function onTouchMove(e) {
    if (!dragging.value) return;
    const touch = e.touches[0];
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    drag.value.style.left = `${
        touch.pageX - (parentRect.left + window.pageXOffset) - offsetX
    }px`;
    drag.value.style.top = `${
        touch.pageY - (parentRect.top + window.pageYOffset) - offsetY
    }px`;
    e.preventDefault();
}

function onTouchEnd(e) {
    dragging.value = false;
    document.removeEventListener("touchmove", onTouchMove);
    document.removeEventListener("touchend", onTouchEnd);

    // Simulate dropping logic using last touch point
    const touch = e.changedTouches[0];

    drag.value.style.pointerEvents = "none";
    const droppedOn = document.elementFromPoint(touch.clientX, touch.clientY);
    drag.value.style.pointerEvents = "";

    if (!droppedOn || !droppedOn.classList.contains("drop")) {
        drag.value.style.left = `${startX}px`;
        drag.value.style.top = `${startY}px`;
        inDrop.value = -1;
        return;
    }

    const rect = droppedOn.getBoundingClientRect();
    const elRect = drag.value.getBoundingClientRect();
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    drag.value.style.left = `${
        rect.left + rect.width / 2 - elRect.width / 2 - parentRect.left
    }px`;
    drag.value.style.top = `${
        rect.top + rect.height / 2 - elRect.height / 2 - parentRect.top
    }px`;

    inDrop.value = droppedOn.dataset.id;
}

function onWindowResize() {
    const parent = drag.value.offsetParent;
    const parentRect = parent.getBoundingClientRect();

    drag.value.style.left = `${relX * parentRect.width}px`;
    drag.value.style.top = `${relY * parentRect.height}px`;
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
