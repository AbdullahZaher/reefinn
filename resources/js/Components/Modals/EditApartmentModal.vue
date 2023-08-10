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
    apartmentTypes: {
        type: Object,
    },
    apartmentDescriptions: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);

const _form = useForm({
    name: props.apartment.name,
    type: props.apartment.type_id,
    description: props.apartment.description_id,
    price_for_night: props.apartment.price_for_night,
});

const _submitHandler = () => {
    _form.clearErrors();

    _form.patch(route("apartments.update", props.apartment.id), {
        onSuccess: () => {
            emit("close");
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Modal
        :headerTitle="`${__('Edit Apartment Information for')} '${
            apartment.name
        }'`"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="!_form.processing"
    >
        <form class="flex flex-wrap items-center gap-4">
            <div class="mb-6 w-full">
                <label
                    for="name"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Name") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>
                <input
                    id="name"
                    type="text"
                    v-model="_form.name"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required
                />
                <p class="text-sm text-red-600 mt-1">{{ _form.errors.name }}</p>
            </div>

            <div class="mb-6 w-full">
                <label
                    for="type"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Room Type") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>
                <select
                    id="type"
                    class="border border-gray-300 text-gray-900 bg-gray-50 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1 rtl"
                    v-model="_form.type"
                >
                    <option disabled>{{ __("Choose a option") }}</option>
                    <option
                        :value="type.id"
                        v-for="(type, index) in apartmentTypes"
                        :key="index"
                    >
                        {{ __(type.value) }}
                    </option>
                </select>
                <p class="text-sm text-red-600 mt-1">
                    {{ _form.errors.type }}
                </p>
            </div>

            <div class="mb-6 w-full">
                <label
                    for="description"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Room Description") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>
                <select
                    id="description"
                    class="border border-gray-300 text-gray-900 bg-gray-50 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1 rtl"
                    v-model="_form.description"
                >
                    <option disabled>{{ __("Choose a option") }}</option>
                    <option
                        :value="description.id"
                        v-for="(description, index) in apartmentDescriptions"
                        :key="index"
                    >
                        {{ __(description.value) }}
                    </option>
                </select>
                <p class="text-sm text-red-600 mt-1">
                    {{ _form.errors.description }}
                </p>
            </div>

            <div class="mb-6 w-full">
                <label
                    for="price_for_night"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Default Price For Night") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>
                <input
                    id="price_for_night"
                    type="number"
                    min="0"
                    step="0.1"
                    v-model="_form.price_for_night"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required
                />
                <p class="text-sm text-red-600 mt-1">
                    {{ _form.errors.price_for_night }}
                </p>
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
                    <span>{{ __("Update") }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>
