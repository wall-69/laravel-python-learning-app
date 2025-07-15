<template>
    <div>
        <div
            class="border px-3 py-2 text-center drop"
            style="width: 240px"
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
