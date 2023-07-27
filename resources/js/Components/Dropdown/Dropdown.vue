<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    title: {
        type: String,
    },
    icon: {
        type: String,
    },
});

let open = ref(false);

let containerElement = ref(null);
let clickedOnComponent = (e) => {
    if (!containerElement.value.contains(e.target)) open.value = false;
};

onMounted(() => window.addEventListener("click", clickedOnComponent));
onUnmounted(() => window.removeEventListener("click", clickedOnComponent));
</script>

<template>
    <div class="relative w-full md:w-fit" ref="containerElement">
        <button
            class="flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 md:w-fit"
            type="button"
            @click="open = !open"
        >
            <FontAwesomeIcon
                :icon="props.icon"
                class="mr-2 rtl:mr-0 rtl:ml-2"
            />
            <span>{{ props.title }}</span>
            <FontAwesomeIcon
                icon="fas fa-caret-down"
                class="ml-2 rtl:ml-0 rtl:mr-2"
            />
        </button>

        <div
            class="absolute top-10 left-0 z-10 w-full divide-y divide-gray-100 rounded bg-white shadow md:w-48"
            v-if="open"
        >
            <ul class="space-y-1 p-3 text-sm text-gray-700">
                <slot />
            </ul>
        </div>
    </div>
</template>
