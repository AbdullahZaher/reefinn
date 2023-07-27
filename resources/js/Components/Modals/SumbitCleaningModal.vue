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
    note: "",
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
        :headerTitle="__('Cleaning Note')"
        :open="open"
        @close="$emit('close')"
    >
        <form class="flex flex-wrap items-center gap-4">
            <div class="mb-6 w-full">
                <label
                    for="note"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Note") }}
                </label>
                <textarea
                    id="note"
                    v-model="_form.note"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required
                ></textarea>
                <p class="text-sm text-red-600 mt-1">{{ _form.errors.note }}</p>
            </div>

            <div
                class="flex items-center justify-end"
                :class="[
                    $page.props.locale.dir == 'ltr' ? 'ml-auto' : 'mr-auto',
                ]"
            >
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
                    <span>{{ __("Update") }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>
