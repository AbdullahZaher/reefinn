<script setup>
defineProps({
    value: {
        type: String,
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700">
        <span v-if="value">
            {{ value }}
            <span class="text-red-600 text-sm" v-if="required">*</span>
        </span>
        <span v-else><slot /></span>
    </label>
</template>
