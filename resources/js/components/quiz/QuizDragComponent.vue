<template>
    <div class="position-relative">
        <div
            ref="drag"
            class="position-absolute border drag z-1 bg-white px-3 py-2 text-center d-flex align-items-center justify-content-center"
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

// Define
const props = defineProps({
    drop: Number,
});

// Lifecycle
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

// Variables
const drag = ref(null);
const inDrop = ref(-1);

let startX = 0,
    startY = 0;
let offsetX = 0,
    offsetY = 0;
const dragging = ref(false);
let relX = 0;
let relY = 0;

// Functions
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
    const elemAtPoint = document.elementFromPoint(e.clientX, e.clientY);
    drag.value.style.pointerEvents = "";

    // Find nearest ancestor with class 'drop' (elementFromPoint might return inner content)
    const droppedOn = elemAtPoint ? elemAtPoint.closest(".drop") : null;

    if (!droppedOn) {
        drag.value.style.left = `${startX}px`;
        drag.value.style.top = `${startY}px`;

        // Restore original height and clear constraints
        drag.value.style.height = "100%";
        drag.value.style.maxHeight = "";
        drag.value.style.minHeight = "";

        inDrop.value = -1;
        // Ensure DOM attribute is set immediately for checks elsewhere
        drag.value.dataset.inDrop = `${-1}`;
        return;
    }

    // Move the drag to the center of drop and adjust height only
    const rect = droppedOn.getBoundingClientRect();
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    // Set height to drop's height (allow shrinking); cap at 150 to preserve existing logic
    const targetHeight = Math.min(rect.height, 150);
    drag.value.style.height = `${targetHeight}px`;
    drag.value.style.maxHeight = `${targetHeight}px`;
    drag.value.style.minHeight = `0px`;

    // Recompute element rect after resizing
    const elRect = drag.value.getBoundingClientRect();

    // Keep width unchanged; center horizontally & vertically
    drag.value.style.left = `${
        rect.left + rect.width / 2 - elRect.width / 2 - parentRect.left
    }px`;
    drag.value.style.top = `${
        rect.top + rect.height / 2 - elRect.height / 2 - parentRect.top
    }px`;

    inDrop.value = droppedOn.dataset.id;
    // Also set the DOM attribute immediately so checks that read attributes (not Vue refs) are reliable
    drag.value.dataset.inDrop = `${droppedOn.dataset.id}`;
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
    const elemAtPoint = document.elementFromPoint(touch.clientX, touch.clientY);
    drag.value.style.pointerEvents = "";

    // Find nearest ancestor with class 'drop' (elementFromPoint might return inner content)
    const droppedOn = elemAtPoint ? elemAtPoint.closest(".drop") : null;

    if (!droppedOn) {
        drag.value.style.left = `${startX}px`;
        drag.value.style.top = `${startY}px`;

        // Restore original height and clear constraints
        drag.value.style.height = "100%";
        drag.value.style.maxHeight = "";
        drag.value.style.minHeight = "";

        inDrop.value = -1;
        // Ensure DOM attribute is set immediately
        drag.value.dataset.inDrop = `${-1}`;
        return;
    }

    const rect = droppedOn.getBoundingClientRect();
    const parentRect = drag.value.offsetParent.getBoundingClientRect();

    // Set height to drop's height (allow shrinking); cap at 150
    const targetHeight = Math.min(rect.height, 150);
    drag.value.style.height = `${targetHeight}px`;
    drag.value.style.maxHeight = `${targetHeight}px`;
    drag.value.style.minHeight = `0px`;

    // Recompute element rect after resizing
    const elRect = drag.value.getBoundingClientRect();

    // Center within drop (width unchanged)
    drag.value.style.left = `${
        rect.left + rect.width / 2 - elRect.width / 2 - parentRect.left
    }px`;
    drag.value.style.top = `${
        rect.top + rect.height / 2 - elRect.height / 2 - parentRect.top
    }px`;

    inDrop.value = droppedOn.dataset.id;
    // Also set the DOM attribute immediately so checks that read attributes (not Vue refs) are reliable
    drag.value.dataset.inDrop = `${droppedOn.dataset.id}`;
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
