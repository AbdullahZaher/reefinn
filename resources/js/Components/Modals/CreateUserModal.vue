<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    permissions: {
        type: Array,
    },
});

const emit = defineEmits(["close"]);

const _form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    max_discount: 0,
    permissions: [],
});

const discountBlurHandler = (event) => {
    _form.max_discount = (Math.trunc(_form.max_discount * 100) / 100).toFixed(
        2
    );
    event.target.value = _form.max_discount;
};

const _submitHandler = () => {
    _form.clearErrors();

    _form.post(route("users.store"), {
        onSuccess: () => {
            emit("close");
        },
    });
};
</script>

<template>
    <Modal :headerTitle="__('Add User')" :open="open" @close="$emit('close')">
        <form class="flex flex-wrap items-center gap-4">
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
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
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.name }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="email"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Email") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        v-model="_form.email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.email }}
                    </p>
                </div>
            </div>

            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="password"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Password") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <input
                        id="password"
                        type="password"
                        v-model="_form.password"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.password }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="password_confirmation"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Confirm Password") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        v-model="_form.password_confirmation"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.password_confirmation }}
                    </p>
                </div>
            </div>

            <div class="mb-6 w-full">
                <label
                    for="max_discount"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Maximum Discount") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>
                <div class="relative">
                    <input
                        id="max_discount"
                        type="number"
                        min="0"
                        max="100"
                        step="0.1"
                        v-model="_form.max_discount"
                        @blur="discountBlurHandler"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ltr:pr-10 rtl:pl-10"
                        required
                    />
                    <span
                        class="absolute top-1/2 transform -translate-y-1/2 ltr:right-3 rtl:left-3 text-lg font-medium select-none"
                    >
                        %
                    </span>
                </div>
                <p class="text-sm text-red-600 mt-1">
                    {{ _form.errors.max_discount }}
                </p>
            </div>

            <div class="mb-6 w-full">
                <div class="mb-2 text-sm font-medium text-gray-900">
                    {{ __("Permissions") }}
                </div>

                <div class="space-y-3">
                    <div
                        class="flex items-center"
                        v-for="permission in permissions"
                        :key="permission"
                    >
                        <input
                            checked
                            :id="permission"
                            type="checkbox"
                            :value="permission"
                            v-model="_form.permissions"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                        />
                        <label
                            :for="permission"
                            class="ml-2 rtl:ml-0 rtl:mr-2 font-medium text-gray-900"
                        >
                            {{
                                __(
                                    permission.replace(
                                        /(^\w{1})|(\s+\w{1})/g,
                                        (letter) => letter.toUpperCase()
                                    )
                                )
                            }}
                        </label>
                    </div>
                </div>

                <p class="text-sm text-red-600 mt-3">
                    {{ _form.errors.permissions }}
                </p>
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
                    <span>{{ __("Add") }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>
