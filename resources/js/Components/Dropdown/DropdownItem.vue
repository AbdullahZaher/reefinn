<script setup>
import { computed } from "vue";
import uniqueId from "lodash-es/uniqueId";

const props = defineProps({
    title: {
        type: String,
    },
    value: {
        type: [Number, String],
    },
    modelValue: {
        type: Array,
    },
});

// Unique id for label and input
const uuid = uniqueId("id");

const emit = defineEmits(["update:modelValue"]);

let model = computed({
    get() {
        return props.modelValue;
    },

    set(value) {
        emit("update:modelValue", value);
    },
});
</script>

<template>
    <li>
        <div
            class="flex items-center p-2 rounded hover:bg-gray-100 cursor-pointer"
        >
            <input
                type="checkbox"
                :id="uuid"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"
                :value="props.value"
                v-model="model"
            />
            <label
                :for="uuid"
                class="ml-2 rtl:ml-0 rtl:mr-2 w-full h-full text-sm font-medium text-gray-900 rounded cursor-pointer"
            >
                {{ props.title }}
            </label>
        </div>
    </li>
</template>
