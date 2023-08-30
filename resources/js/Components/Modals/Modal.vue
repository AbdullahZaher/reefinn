<script setup>
import { onMounted, onUnmounted, watch } from "vue";
import { vOnClickOutside } from "@vueuse/components";

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    closable: {
        type: Boolean,
        default: true,
    },
    headerTitle: {
        type: String,
        default: "",
    },
    clickOutsideToClose: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["close"]);

const closeModalWhenEsc = (event) => {
    if (event.keyCode == 27) emit("close");
};

const closeWhenOutside = () => {
    if (props.closable && props.clickOutsideToClose) emit("close");
};

onMounted(() => {
    if (props.closable) document.addEventListener("keydown", closeModalWhenEsc);
    document.documentElement.classList.add("overflow-y-hidden");
});

onUnmounted(() => {
    if (props.closable)
        document.removeEventListener("keydown", closeModalWhenEsc);
    document.documentElement.classList.remove("overflow-y-hidden");
});

watch(
    () => props.open,
    (openState) => {
        if (openState) {
            document.documentElement.classList.add("overflow-y-hidden");
        } else {
            document.documentElement.classList.remove("overflow-y-hidden");
        }
    }
);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-75 ease-in="
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-75 ease-out"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                tabindex="-1"
                class="bg-black/70 overflow-y-auto overflow-x-hidden fixed w-full inset-0 max-h-screen z-40"
                @keydown.esc="$emit('close')"
                v-if="open"
            >
                <div
                    class="mt-16 mb-24 px-4 w-full max-w-3xl mx-auto z-50"
                    v-on-click-outside="closeWhenOutside"
                    v-if="open"
                >
                    <div class="relative bg-white rounded-lg shadow">
                        <div
                            class="flex justify-between items-start p-4 rounded-t border-b"
                        >
                            <div
                                class="text-2xl font-bold text-gray-900"
                                v-if="!$slots.header && headerTitle"
                                v-html="headerTitle"
                            ></div>

                            <slot name="header" />

                            <button
                                type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center transition duration-100"
                                :class="[
                                    $page.props.locale.dir == 'ltr'
                                        ? 'ml-auto'
                                        : 'mr-auto',
                                ]"
                                @click="$emit('close')"
                                v-if="closable"
                            >
                                <FontAwesomeIcon icon="fas fa-xmark" />
                            </button>
                        </div>

                        <div class="p-6">
                            <slot />
                        </div>

                        <div
                            class="rounded-b border-t border-gray-200"
                            v-if="$slots.footer"
                        >
                            <slot name="footer" />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
