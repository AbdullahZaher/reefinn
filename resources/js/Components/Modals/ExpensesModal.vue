<script setup>
import { ref } from "vue";
import Modal from "@/Components/Modals/Modal.vue";
import { useForm, router } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import Swal from "sweetalert2";
import SwitchableDatePicker from "@/Components/SwitchableDatePicker.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    expenses: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);

const clickOutsideToClose = ref(true);

const _form = useForm({
    item: "",
    price: "",
    employee: "",
    date: new Date().toJSON().slice(0, 10),
});

const _submitHandler = () => {
    _form.clearErrors();

    _form.post(route("finance.expenses.store"), {
        preserveState: true,
        onSuccess: () => {
            _form.reset();
        },
    });
};

const deleteExpense = (id) => {
    clickOutsideToClose.value = false;

    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, delete expense!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.delete(route("finance.expenses.destroy", id), {
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
        :headerTitle="__('Expenses')"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="clickOutsideToClose"
    >
        <form
            class="flex flex-wrap items-center gap-4"
            v-if="$page.props.auth.user.can.includes('create expenses')"
        >
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="item"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Item") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <input
                        id="item"
                        type="text"
                        v-model="_form.item"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.item }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="price"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Price") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <input
                        id="price"
                        type="number"
                        min="0"
                        max="999999.99"
                        step="0.1"
                        v-model="_form.price"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.price }}
                    </p>
                </div>
            </div>

            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="employee"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Employee") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <input
                        id="employee"
                        type="text"
                        v-model="_form.employee"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.employee }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="date"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("DateField") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <SwitchableDatePicker v-model="_form.date" />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.date }}
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
                            {{ __("Item") }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Price") }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Employee") }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Date") }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-center">
                            {{ __("Actions") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b"
                        v-for="(expense, index) in expenses.data"
                        :key="expense.id"
                    >
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                        >
                            {{ index + 1 }}
                        </th>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ expense.item }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ expense.price + " " + __("SAR") }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ expense.employee }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ expense.date }}
                        </td>
                        <td
                            class="px-6 py-4 flex items-center justify-center gap-5 whitespace-nowrap"
                        >
                            <button
                                @click="deleteExpense(expense.id)"
                                :title="__('Delete Expense')"
                                v-if="
                                    $page.props.auth.user.can.includes(
                                        'delete expenses'
                                    )
                                "
                            >
                                <FontAwesomeIcon icon="fas fa-trash" />
                            </button>

                            <span
                                v-if="
                                    !$page.props.auth.user.can.includes(
                                        'delete expenses'
                                    )
                                "
                                >-</span
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Modal>
</template>
