<script setup>
import { computed } from "vue";

const props = defineProps({
    size: {
        default: "40px",
    },
    background: {
        default: "#41b883",
    },
    color: {
        default: "#35495e",
    },
    stroke: {
        default: "5px",
    },
});

const styles = computed(() => {
    return {
        width: props.size,
        height: props.size,
        border: props.stroke + " solid " + props.background,
        borderTopColor: props.color,
    };
});
</script>

<style scoped>
.spinner {
    box-sizing: border-box;
    border-radius: 100%;
    border: 10px solid rgba(255, 255, 255, 0.2);
    animation: circle-2-spin 1s infinite linear;
}
@keyframes circle-2-spin {
    100% {
        transform: rotate(360deg);
    }
}
</style>

<template>
    <div :style="styles" class="spinner"></div>
</template>
