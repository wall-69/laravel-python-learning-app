<template>
    <div class="alerts-container">
        <alert
            v-for="alert in alerts"
            :type="alert.type"
            :message="alert.message"
        ></alert>
    </div>
</template>
<script setup>
import { onMounted } from "vue";
import { addAlert, alerts } from "../helpers";

// Define
const props = defineProps({
    alerts: Object,
});

// Lifecycle
onMounted(() => {
    for (let alertType of Object.keys(props.alerts)) {
        addAlert(alertType, props.alerts[alertType]);
    }
});
</script>
