<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import SwitchableDatePicker from "@/Components/SwitchableDatePicker.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    apartment: {
        type: Object,
    },
});

const emit = defineEmits(["close", "closeParent"]);

const _form = useForm({
    checkin: new Date().toISOString().split("T")[0],
    checkout: new Date(Date.now() + 24 * 60 * 60 * 1000)
        .toISOString()
        .split("T")[0],
    guest_id: "",
    guest_birthday: "",
    guest_name: "",
    guest_phone: "",
    number_of_companions: "",
    copy: "",
    price_for_night: props.apartment.price_for_night,
    note: "",
});

const updateTotalPrice = () => {
    let totalPrice = 0;

    if (
        _form.checkin &&
        _form.checkout &&
        new Date(_form.checkin) <= new Date(_form.checkout)
    ) {
        const date1 = new Date(_form.checkin);
        const date2 = new Date(_form.checkout);
        const timeDiff = Math.abs(date2.getTime() - date1.getTime());
        let numberOfNights = Math.ceil(timeDiff / (1000 * 3600 * 24));
        if (_form.checkin == _form.checkout) numberOfNights = 1;

        totalPrice = _form.price_for_night * numberOfNights;
    }

    _form.total_price = `${new Intl.NumberFormat("en-US", {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
    }).format(totalPrice)} ${__("SAR")}`;
};
updateTotalPrice();
watch(_form, updateTotalPrice);

const _submitHandler = () => {
    const prevState = props.apartment.state.toLowerCase();

    _form.clearErrors();

    _form.post(route("reservations.store", props.apartment.id), {
        preserveScroll: true,
        onSuccess: () => {
            _form.reset();
            emit("close");

            if (
                props.apartment.state.toLowerCase() == "inhabited" &&
                prevState != "inhabited"
            )
                emit("closeParent");
        },
    });
};
</script>

<template>
    <Modal
        :headerTitle="`${__('Add Reservation For')} '${apartment.name}'`"
        :open="open"
        @close="$emit('close')"
    >
        <form class="flex flex-wrap items-center gap-4">
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="name"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Guest Name") }}
                    </label>
                    <input
                        id="name"
                        type="text"
                        v-model="_form.guest_name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.guest_name }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="phone"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Guest Phone") }}
                    </label>
                    <input
                        id="phone"
                        type="text"
                        v-model="_form.guest_phone"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.guest_phone }}
                    </p>
                </div>
            </div>
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="guest_id"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Guest ID") }}
                    </label>
                    <input
                        id="guest_id"
                        type="text"
                        v-model="_form.guest_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.guest_id }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="guest_birthday"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Guest Birthday") }}
                    </label>
                    <SwitchableDatePicker v-model="_form.guest_birthday" />
                    <p class="text-sm text-red-600 mt-3 text-center">
                        {{ _form.errors.guest_birthday }}
                    </p>
                </div>
                <div class="flex-1 w-full md:flex-initial md:w-20">
                    <label
                        for="number_of_companions"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Number Of Companions") }}
                    </label>
                    <input
                        id="number_of_companions"
                        type="number"
                        min="0"
                        max="50"
                        step="1"
                        v-model="_form.number_of_companions"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.number_of_companions }}
                    </p>
                </div>
                <div class="flex-1 w-full md:flex-initial md:w-20">
                    <label
                        for="copy"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Copy") }}
                    </label>
                    <input
                        id="copy"
                        type="number"
                        min="0"
                        max="50"
                        step="1"
                        v-model="_form.copy"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.copy }}
                    </p>
                </div>
            </div>
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="price_for_night"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Price For Night") }}
                    </label>
                    <input
                        id="price_for_night"
                        type="number"
                        min="0"
                        step="0.1"
                        v-model="_form.price_for_night"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                        :disabled="
                            !$page.props.auth.user.can.includes(
                                'change reservation price for night'
                            )
                        "
                    />
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.price_for_night }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="total_price"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Total Price") }}
                    </label>
                    <input
                        id="total_price"
                        type="text"
                        v-model="_form.total_price"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                        disabled
                    />
                </div>
            </div>
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="checkin"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Check-in") }}
                    </label>
                    <SwitchableDatePicker v-model="_form.checkin" />
                    <p class="text-sm text-red-600 mt-3 text-center">
                        {{ _form.errors.checkin }}
                    </p>
                </div>
                <div class="flex-1 w-full">
                    <label
                        for="checkout"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Check-out") }}
                    </label>
                    <SwitchableDatePicker v-model="_form.checkout" />
                    <p class="text-sm text-red-600 mt-3 text-center">
                        {{ _form.errors.checkout }}
                    </p>
                </div>
            </div>
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
                    <span>{{ __("Add") }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>
