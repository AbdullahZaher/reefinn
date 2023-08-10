<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    apartment: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);

const _form = useForm({
    rating: "",
});

const _submitHandler = () => {
    _form.patch(route("apartments.update.state", props.apartment.id), {
        preserveScroll: true,
        onSuccess: () => {
            _form.reset();
            emit("close");
        },
    });
};
</script>

<template>
    <Modal
        :headerTitle="__('Checkout Reservation For') + ' ' + apartment.name"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="!_form.processing"
    >
        <form class="flex flex-wrap items-center gap-4">
            <div class="mb-6 w-full">
                <label
                    for="rating"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Guest Rating") }}
                </label>
                <div class="flex items-center justify-center gap-1 mt-2">
                    <button
                        type="button"
                        v-for="(i, j) in Array.from({ length: 5 })"
                        :key="j"
                        @click="_form.rating = j + 1"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-star"
                            class="text-yellow-400 text-3xl"
                            v-if="_form.rating >= j + 1"
                        />
                        <FontAwesomeIcon
                            icon="far fa-star"
                            class="text-yellow-400 text-3xl"
                            v-else
                        />
                    </button>
                </div>
                <p class="text-sm text-red-600 mt-1">{{ _form.errors.note }}</p>
            </div>

            <div class="flex items-center justify-end ltr:ml-auto rtl:mr-auto">
                <button
                    type="submit"
                    class="text-white bg-blue-700 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 flex items-center justify-center space-x-2 rtl:space-x-reverse"
                    :class="[
                        _form.processing
                            ? 'bg-opacity-50'
                            : 'hover:bg-blue-800',
                    ]"
                    @click.prevent="_submitHandler"
                    :disabled="_form.processing"
                >
                    <Loader v-if="_form.processing" />
                    <span>{{ __("Check-out") }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>
