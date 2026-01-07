<template>
    <li :data-type="type">
        <p
            class="mb-0 fw-bold d-flex align-items-center gap-1"
            :class="{
                'text-success': reveal && correct,
                'text-danger': reveal && !correct,
            }"
        >
            <slot name="question"></slot>
            <i v-if="reveal && correct" class="bx bx-check bx-md fw-bold"></i>
            <i v-else-if="reveal && !correct" class="bx bx-x fw-bold"></i>
        </p>

        <div
            class="answers d-flex flex-column flex-md-row flex-md-wrap gap-3 gap-md-5"
        >
            <div ref="dropsCol" class="d-flex flex-column gap-2">
                <slot name="drops"></slot>
            </div>
            <div ref="dragsCol" class="d-flex flex-column gap-2">
                <slot name="drags"></slot>
            </div>
        </div>

        <button
            @click.prevent="resetPosition"
            class="btn btn-secondary btn-block mt-1 d-flex align-items-center gap-1"
        >
            <i class="bx bxs-eraser"></i> Resetovať
        </button>

        <div
            v-show="reveal"
            class="border px-3 py-2 mt-1"
            :class="{
                'bg-success text-bg-success': correct,
                'bg-danger text-bg-danger': !correct,
            }"
        >
            <span class="fw-bold">
                {{ correct ? "Správne!" : "Nesprávne!" }} <br />
            </span>
            <a
                v-if="!correct && slots.explanation"
                class="text-bg-danger"
                data-bs-toggle="collapse"
                :href="'#explanation_' + questionNumber"
            >
                Zobraziť vysvetlenie
            </a>

            <div
                :id="'explanation_' + questionNumber"
                class="collapse"
                :class="{
                    show: correct,
                }"
            >
                <slot name="explanation"></slot>
            </div>
        </div>
    </li>
</template>
<script setup>
import {
    computed,
    inject,
    nextTick,
    onMounted,
    provide,
    ref,
    useSlots,
} from "vue";

// Composables
const slots = useSlots();

// Define
const props = defineProps({
    type: String,
    questionNumber: Number,
});

// Lifecycle
onMounted(async () => {
    await nextTick();

    // Randomize drag positions
    for (let i = dragsCol.value.children.length; i >= 0; i--) {
        dragsCol.value.appendChild(
            dragsCol.value.children[(Math.random() * i) | 0]
        );
    }

    for (let i = 0; i < dropsCol.value.children.length; i++) {
        const dropEl = dropsCol.value.children[i].firstElementChild;
        const dragEl = dragsCol.value.children[i].firstElementChild;

        const dropHeight = dropEl.offsetHeight;
        const dragHeight = dragEl.offsetHeight;
        const maxHeight = Math.max(dropHeight, dragHeight);

        dropEl.style.height = `${Math.min(maxHeight, 150)}px`;
        dragEl.style.minHeight = `${Math.min(maxHeight, 150)}px`;
        dragEl.parentElement.style.minHeight = `${Math.min(maxHeight, 150)}px`;
    }
});

// Variables
const dropsCol = ref(null);
const dragsCol = ref(null);

const reveal = inject("reveal");
const questionMap = inject("questionMap");

provide("questionNumber", props.questionNumber);

// Computed
const correct = computed(() => questionMap[props.questionNumber]);

// Functions
function resetPosition() {
    if (!dragsCol.value) return;

    const drags = dragsCol.value.querySelectorAll(".drag");

    drags.forEach((d) => {
        // Remove inline left/top so the element returns to its original layout
        d.style.left = "";
        d.style.top = "";

        d.style.height = "100%";

        // Clear any drop-imposed height constraints
        d.style.maxHeight = "";
        d.style.minHeight = "";

        // Ensure the data-in-drop attribute is reset for checks
        d.dataset.inDrop = -1;
    });
}
</script>
