<template>
    <div>
        <div
            class="border px-3 py-2 text-center drop bg-secondary text-bg-secondary d-grid align-items-center"
            style="width: 285.6px"
            :data-id="id"
        >
            <slot></slot>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    id: Number,
});
</script>
