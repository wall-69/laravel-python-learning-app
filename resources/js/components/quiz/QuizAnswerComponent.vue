<template>
    <div class="answer form-check">
        <!-- radio -->
        <template v-if="type == 'radio'">
            <input
                class="form-check-input"
                type="radio"
                :name="questionNumber"
                :id="questionNumber + '_' + id"
                :data-correct="correct"
            />
            <label class="form-check-label" :for="questionNumber + '_' + id">
                <slot></slot>
            </label>
        </template>

        <!-- checkbox -->
        <template v-if="type == 'checkbox'">
            <input
                class="form-check-input"
                type="checkbox"
                :id="questionNumber + '_' + id"
                :data-correct="correct"
            />
            <label class="form-check-label" :for="questionNumber + '_' + id">
                <slot></slot>
            </label>
        </template>
    </div>
</template>
<script setup>
import { inject } from "vue";

// Define
const props = defineProps({
    id: Number,
    type: String,
    correct: {
        type: Boolean,
        default: false,
    },
});

// Variables
const questionNumber = inject("questionNumber");
</script>
