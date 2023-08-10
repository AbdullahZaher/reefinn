<script setup>
import { onMounted, ref, watch } from "vue";

const props = defineProps({
    toast: {
        type: Object,
    },
});

const duration = props.toast?.duration || 5000;

const visible = ref(false);
const timeout = ref(null);

const show = () => {
    if (sessionStorage && props.toast) {
        if (!sessionStorage.getItem(`shown-${props.toast.id}`)) {
            visible.value = true;

            if (timeout.value) clearTimeout(timeout.value);

            timeout.value = setTimeout(() => {
                visible.value = false;
            }, duration);
        }
        sessionStorage.setItem(`shown-${props.toast.id}`, true);
    } else {
        visible.value = true;

        if (timeout.value) clearTimeout(timeout.value);

        timeout.value = setTimeout(() => {
            visible.value = false;
        }, duration);
    }
};

const dismiss = () => {
    visible.value = false;

    if (timeout.value) clearTimeout(timeout.value);
};

onMounted(show);
watch(() => props.toast, show, { deep: true });
</script>

<template>
    <Transition
        enter-active-class="duration-200 ease-in-out transform"
        enter-from-class="translate-y-96 lg:translate-y-0 lg:translate-x-96"
        enter-to-class="translate-y-0 lg:translate-x-0"
        leave-active-class="duration-200 ease-in-out transform"
        leave-from-class="translate-y-0 lg:translate-x-0"
        leave-to-class="translate-y-96 lg:translate-y-0 lg:translate-x-96"
    >
        <div
            class="fixed bottom-0 lg:bottom-auto lg:top-0 lg:right-0 z-50 mt-4 flex w-full lg:max-w-xs overflow-x-hidden overflow-y-auto lg:rounded max-h-96 text-white shadow rtl:lg:mr-4 ltr:lg:ml-4"
            :class="{
                'bg-green-600': toast.type == 'success',
                'bg-red-600': toast.type == 'error',
            }"
            v-if="visible && toast"
        >
            <div class="p-4 text-xl flex items-center">
                <FontAwesomeIcon
                    icon="fas fa-circle-check"
                    class="text-white text-2xl"
                    v-if="toast.type == 'success'"
                />
                <FontAwesomeIcon
                    icon="fas fa-circle-xmark"
                    class="text-white text-2xl"
                    v-if="toast.type == 'error'"
                />
            </div>

            <div class="flex-1 break-words py-4">
                {{ toast.message }}
            </div>

            <button class="foucs:outline-none min-h-full p-4" @click="dismiss">
                <FontAwesomeIcon icon="fas fa-xmark" class="text-white" />
            </button>
        </div>
    </Transition>
</template>
