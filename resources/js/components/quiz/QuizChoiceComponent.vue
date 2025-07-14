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

        <slot name="answers"></slot>

        <div
            v-show="reveal"
            class="border px-3 py-2"
            :class="{
                'bg-success text-bg-success': correct,
                'bg-danger text-bg-danger': !correct,
            }"
        >
            <span class="fw-bold">
                {{ correct ? "Správne!" : "Nesprávne!" }} <br />
            </span>
            <a
                v-if="!correct"
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
import { computed, inject, provide } from "vue";

const props = defineProps({
    type: String,
    questionNumber: String,
});

provide("questionNumber", props.questionNumber);
const reveal = inject("reveal");
const questionMap = inject("questionMap");

const correct = computed(() => questionMap[props.questionNumber]);
</script>
