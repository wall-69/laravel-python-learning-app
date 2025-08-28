<template>
    <div
        class="alert alert-dismissible fade show d-flex align-items-center gap-2"
        :class="alertClass"
        style="min-width: 320px"
        role="alert"
    >
        <i v-if="type == 'success'" class="bx bx-md bxs-check-circle"></i>
        <i v-else-if="type == 'warning'" class="bx bx-md bxs-alert-circle"></i>
        <i v-else-if="type == 'danger'" class="bx bx-md bxs-fire"></i>
        <i v-else-if="type == 'celebrate'" class="bx bx-md bxs-party"></i>
        <i
            v-else-if="type == 'level-up'"
            class="bx bx-md bxs-arrow-big-up-line"
        ></i>

        {{ message }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
    </div>
</template>
<script setup>
import { computed } from "vue";

// Define
const props = defineProps({
    type: String,
    message: String,
});

// Computed
const alertClass = computed(() => {
    switch (props.type) {
        case "celebrate":
            return "alert-info";

        case "level-up":
            return "alert-primary";

        default:
            return "alert-" + props.type;
    }
});
</script>
