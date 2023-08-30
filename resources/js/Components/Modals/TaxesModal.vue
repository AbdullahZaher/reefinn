<script setup>
import { ref } from "vue";
import Modal from "@/Components/Modals/Modal.vue";
import { useForm, router } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    taxes: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);

const clickOutsideToClose = ref(true);

const _form = useForm({
    name: "",
    percentage: "",
});

const _submitHandler = () => {
    _form.clearErrors();

    _form.post(route("finance.taxes.store"), {
        preserveState: true,
        onSuccess: () => {
            _form.reset();
        },
    });
};

const deleteTax = (id) => {
    clickOutsideToClose.value = false;

    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, delete tax!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.delete(route("finance.taxes.destroy", id), {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: resolve,
                });
            }),
    }).finally(() => {
        clickOutsideToClose.value = true;
    });
};
</script>

<template>
    <Modal
        :headerTitle="__('Taxes')"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="clickOutsideToClose && !_form.processing"
    >
        <form
            class="flex flex-wrap items-center gap-4"
            v-if="$page.props.auth.user.can.includes('create taxes')"
        >
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="name"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Tax Name") }}
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
                        for="percentage"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Percentage") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <div class="relative">
                        <input
                            id="percentage"
                            type="number"
                            min="0"
                            max="100"
                            step="0.1"
                            v-model="_form.percentage"
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
                        {{ _form.errors.percentage }}
                    </p>
                </div>
            </div>

            <div class="w-full">
                <button
                    type="submit"
                    class="text-white bg-blue-700 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 flex items-center justify-center space-x-2 rtl:space-x-reverse w-full"
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

        <hr
            class="my-12"
            v-if="$page.props.auth.user.can.includes('create expenses')"
        />

        <div class="overflow-x-scroll max-h-[40vh]">
            <table
                class="w-full text-sm text-left rtl:text-right text-gray-500"
            >
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">#</th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Tax Name") }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Percentage") }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            {{ __("Actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b"
                        v-for="(tax, index) in taxes.data"
                        :key="tax.id"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                        >
                            {{ index + 1 }}
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ tax.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ tax.percentage }}%
                        </td>
                        <td
                            class="px-6 py-4 flex items-center justify-center gap-5 whitespace-nowrap"
                        >
                            <button
                                @click="deleteTax(tax.id)"
                                :title="__('Delete Tax')"
                                v-if="
                                    $page.props.auth.user.can.includes(
                                        'delete taxes'
                                    )
                                "
                            >
                                <FontAwesomeIcon icon="fas fa-trash" />
                            </button>

                            <span
                                v-if="
                                    !$page.props.auth.user.can.includes(
                                        'delete taxes'
                                    )
                                "
                            >
                                -
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Modal>
</template>
