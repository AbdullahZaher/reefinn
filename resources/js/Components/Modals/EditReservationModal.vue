<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { watch, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import SwitchableDatePicker from "@/Components/SwitchableDatePicker.vue";
import TransferGuestModal from "@/Components/Modals/TransferGuestModal.vue";
import { convertIfHijri } from "@/Composables/convertIfHijri";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    apartment: {
        type: Object,
    },
    reservation: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);

const isTransferGuestModalOpen = ref(false);

const _form = useForm({
    checkin: convertIfHijri(props.reservation.checkin, usePage()),
    checkout: convertIfHijri(props.reservation.checkout, usePage()),
    guest_id: props.reservation.guest_id,
    guest_birthday: convertIfHijri(props.reservation.guest_birthday, usePage()),
    guest_name: props.reservation.guest_name,
    guest_phone: props.reservation.guest_phone,
    number_of_companions: props.reservation.number_of_companions,
    copy: props.reservation.copy,
    price_for_night: props.reservation.price_for_night.replace(",", ""),
    total_price: props.reservation.total_price,
    discount: props.reservation.discount,
    note: props.reservation.note,
    amounts_due: props.reservation.amounts_due.replace(",", ""),
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

        totalPrice =
            _form.price_for_night * numberOfNights -
            (_form.discount / 100) * _form.price_for_night * numberOfNights;
    }

    if (totalPrice < 0) totalPrice = 0;

    _form.total_price = `${new Intl.NumberFormat("en-US", {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
    }).format(totalPrice)} ${__("SAR")}`;
};
updateTotalPrice();
watch(_form, updateTotalPrice);

const discountBlurHandler = (event) => {
    _form.discount = (Math.trunc(_form.discount * 100) / 100).toFixed(2);
    event.target.value = _form.discount;
};

const _submitHandler = () => {
    _form.clearErrors();

    _form.patch(route("reservations.update", props.reservation.id), {
        onSuccess: () => {
            emit("close");
        },
    });
};
</script>

<template>
    <TransferGuestModal
        :open="isTransferGuestModalOpen"
        @close="isTransferGuestModalOpen = false"
        :reservation="reservation"
        :apartmentId="apartment.id"
        v-if="
            isTransferGuestModalOpen &&
            $page.props.auth.user.can.includes('transfer reservations')
        "
    />

    <Modal
        :headerTitle="`${__('Edit Reservation Information for')} '${
            reservation.guest_name
        }'`"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="!isTransferGuestModalOpen"
    >
        <form class="flex flex-wrap items-center gap-4">
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="name"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Guest Name") }}
                        <span class="text-red-600 text-sm">*</span>
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
                        <span class="text-red-600 text-sm">*</span>
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
                        <span class="text-red-600 text-sm">*</span>
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
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <SwitchableDatePicker v-model="_form.guest_birthday" />
                    <p class="text-sm text-red-600 mt-3 text-center">
                        {{ _form.errors.guest_birthday }}
                    </p>
                </div>
                <div class="flex-1 w-full md:flex-initial md:w-24">
                    <label
                        for="number_of_companions"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Number Of Companions") }}
                        <span class="text-red-600 text-sm">*</span>
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
                <div class="flex-1 w-full md:flex-initial md:w-24">
                    <label
                        for="copy"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Copy") }}
                        <span class="text-red-600 text-sm">*</span>
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
                <div class="flex-1 w-full">
                    <label
                        for="discount"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Discount") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <div class="relative">
                        <input
                            id="discount"
                            type="number"
                            min="0"
                            :max="$page.props.auth.user.max_discount"
                            step="0.1"
                            v-model="_form.discount"
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
                        {{ _form.errors.discount }}
                    </p>
                </div>
            </div>
            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="flex-1 w-full">
                    <label
                        for="checkin"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Check-in") }}
                        <span class="text-red-600 text-sm">*</span>
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
                        <span class="text-red-600 text-sm">*</span>
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

            <div class="mb-6 w-full">
                <label
                    for="amounts_due"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Amounts Due") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>
                <input
                    id="amounts_due"
                    type="number"
                    min="0"
                    step="0.1"
                    v-model="_form.amounts_due"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required
                />
                <p class="text-sm text-red-600 mt-1">
                    {{ _form.errors.amounts_due }}
                </p>
            </div>

            <div
                class="flex items-center justify-between"
                :class="[
                    $page.props.auth.user.can.includes('transfer reservations')
                        ? 'w-full'
                        : 'ltr:ml-auto rtl:mr-auto',
                ]"
            >
                <button
                    type="button"
                    class="text-blue-700 focus:outline-none font-medium rounded-lg text-sm py-2.5 hover:text-blue-800"
                    @click="isTransferGuestModalOpen = true"
                    v-if="
                        $page.props.auth.user.can.includes(
                            'transfer reservations'
                        )
                    "
                >
                    {{ __("Transfer Guest To Another Apartment") }}
                </button>

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
