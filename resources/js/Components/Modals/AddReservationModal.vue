<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { nextTick, ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import SwitchableDatePicker from "@/Components/SwitchableDatePicker.vue";
import { vOnClickOutside } from "@vueuse/components";
import { VueTelInput } from "vue-tel-input";
import { __ } from "@/Composables/translations";

import "vue-tel-input/vue-tel-input.css";

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    apartment: {
        type: Object,
    },
    checkin: {
        type: String,
        default: new Date().toISOString().split("T")[0],
    },
    checkout: {
        type: String,
        default: new Date(Date.now() + 24 * 60 * 60 * 1000)
            .toISOString()
            .split("T")[0],
    },
    types: {
        type: Object,
    },
    idTypes: {
        type: Object,
    },
    paymentMethods: {
        type: Object,
    },
    forceParentClose: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close", "closeParent"]);

const showDates = ref(true);

const isSubmitButtonDropdownOpen = ref(false);
const closeSubmitButtonDropdown = (e) => {
    if (!e.target.classList.contains("no-outside-click") && !_form.processing)
        isSubmitButtonDropdownOpen.value = false;
};

const _form = useForm({
    type: "1",
    checkin: props.checkin,
    checkout: props.checkout,
    id_type: "1",
    guest_id: "",
    guest_birthday: "",
    guest_name: "",
    guest_phone: "",
    number_of_companions: "",
    copy: "",
    price_for_night: props.apartment.price_for_night.replace(",", ""),
    discount: "0.00",
    note: "",
    amounts_due: "0.00",
    payment_method: "",
    auto_renew: false,
    checkin_now: false,
});

watch(
    () => _form.type,
    async (value) => {
        _form.checkin = new Date().toISOString().split("T")[0];

        switch (value) {
            case "1":
                _form.checkout = new Date(Date.now() + 24 * 60 * 60 * 1000)
                    .toISOString()
                    .split("T")[0];
                break;
            case "2":
                _form.checkout = new Date(Date.now() + 24 * 60 * 60 * 1000 * 7)
                    .toISOString()
                    .split("T")[0];
                break;
            case "3":
                _form.checkout = new Date(Date.now() + 24 * 60 * 60 * 1000 * 30)
                    .toISOString()
                    .split("T")[0];
                break;
        }

        // Refresh State
        showDates.value = false;
        await nextTick();
        showDates.value = true;
    }
);

const updateTotalPrice = () => {
    let totalPrice = 0;
    let taxes = 0;

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

        totalPrice -= (_form.discount / 100) * totalPrice;

        taxes = totalPrice * (usePage().props.app.tax_percentage / 100);
        totalPrice += taxes;
    }

    if (taxes < 0) taxes = 0;
    if (totalPrice < 0) totalPrice = 0;

    _form.taxes_amount = `${new Intl.NumberFormat("en-US", {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
    }).format(taxes)} ${__("SAR")}`;

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

const _submitHandler = (e, checkinNow = false) => {
    const prevState = props.apartment.state.toLowerCase();

    _form.checkin_now = checkinNow;
    if (!_form.checkin_now) isSubmitButtonDropdownOpen.value = false;

    _form.clearErrors();

    _form.post(route("reservations.store", props.apartment.id), {
        onSuccess: () => {
            _form.reset();
            emit("close");

            if (
                (props.apartment.state.toLowerCase() == "inhabited" &&
                    prevState != "inhabited") ||
                props.forceParentClose
            )
                emit("closeParent");
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <Modal
        :headerTitle="`${__('Add Reservation For')} '${apartment.name}'`"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="!_form.processing"
    >
        <form class="flex flex-wrap items-center gap-4">
            <div class="mx-auto flex-1 flex items-center gap-3 mb-3">
                <label
                    for="type"
                    class="block text-sm font-medium text-gray-900 shrink-0"
                >
                    {{ __("Rent Method") }}
                    <span class="text-red-600 text-sm">*</span> :
                </label>

                <div class="flex w-full relative">
                    <template v-for="(type, key) in types" :key="key">
                        <input
                            type="radio"
                            :id="`option-${key}`"
                            name="tabs"
                            class="appearance-none hidden"
                            v-model="_form.type"
                            :value="key"
                        />
                        <label
                            :for="`option-${key}`"
                            class="cursor-pointer w-1/6 flex items-center justify-center truncate uppercase select-none font-semibold text-lg rounded-full py-0.5 z-10"
                            :class="{
                                'text-white': _form.type == key,
                            }"
                        >
                            {{ __(type) }}
                        </label>
                    </template>
                    <div
                        class="w-1/6 flex items-center justify-center truncate uppercase select-none font-semibold text-lg rounded-full p-0 h-full bg-indigo-600 absolute transform transition-transform tabAnim"
                    ></div>
                </div>
            </div>

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
                    <VueTelInput
                        v-model="_form.guest_phone"
                        :dropdownOptions="{
                            disabled: false,
                            showSearchBox: true,
                            showFlags: true,
                            searchPlaceholder: __('Search...'),
                            showDialCodeInList: false,
                        }"
                        :inputOptions="{
                            required: true,
                            showDialCode: true,
                            placeholder: __('Phone Number'),
                        }"
                        mode="international"
                    ></VueTelInput>
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.guest_phone }}
                    </p>
                </div>
            </div>

            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
                <div class="md:w-1/3 w-full">
                    <label
                        for="id_type"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("ID Type") }}
                        <span class="text-red-600 text-sm">*</span>
                    </label>
                    <select
                        id="id_type"
                        class="border border-gray-300 text-gray-900 bg-gray-50 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1 rtl"
                        v-model="_form.id_type"
                    >
                        <option disabled>
                            {{ __("Choose a option") }}
                        </option>
                        <option
                            :value="id_type.id"
                            v-for="(id_type, index) in idTypes"
                            :key="index"
                        >
                            {{ __(id_type.value) }}
                        </option>
                    </select>
                    <p class="text-sm text-red-600 mt-1">
                        {{ _form.errors.id_type }}
                    </p>
                </div>
                <div class="md:w-2/3 w-full">
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
            </div>

            <div class="mb-6 flex gap-6 w-full flex-col md:flex-row">
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
                <div class="flex-1 w-full md:flex-initial md:w-32">
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
                <div class="flex-1 w-full md:flex-initial md:w-32">
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
                        for="taxes"
                        class="block mb-2 text-sm font-medium text-gray-900"
                    >
                        {{ __("Taxes") }}
                    </label>
                    <input
                        id="taxes"
                        type="text"
                        v-model="_form.taxes_amount"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required
                        disabled
                    />
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
                <div
                    class="flex-1 w-full"
                    v-if="$page.props.auth.user.max_discount > 0"
                >
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
                <div class="flex-1 w-full">
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
            </div>

            <div
                class="mb-6 flex gap-6 w-full flex-col md:flex-row"
                v-if="showDates"
            >
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
                    for="payment_method"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Payment Method") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>
                <div class="flex justify-center items-center flex-wrap gap-8">
                    <div
                        class="border p-2 h-36 flex items-center gap-5 cursor-pointer"
                        :class="[
                            _form.payment_method == method.id
                                ? 'border-blue-500 border-2'
                                : 'border',
                        ]"
                        v-for="method in paymentMethods"
                        :key="method.id"
                        @click="_form.payment_method = method.id"
                    >
                        <input
                            type="radio"
                            name="method"
                            v-model="_form.payment_method"
                            :value="method.id"
                        />
                        <img :src="method.image_url" class="w-20" />
                    </div>
                </div>
                <p class="text-sm text-red-600 mt-1">
                    {{ _form.errors.payment_method }}
                </p>
            </div>

            <div
                class="mb-6 w-full flex items-center md:flex-row flex-col justify-center gap-5"
            >
                <label
                    for="payment_method"
                    class="block text-sm font-medium text-gray-900"
                >
                    {{ __("Auto Renew") }}:
                </label>

                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                    <span class="text-sm font-medium">
                        {{ __("Off") }}
                    </span>
                    <button
                        type="button"
                        class="relative rounded-full focus:outline-none"
                        @click="_form.auto_renew = !_form.auto_renew"
                    >
                        <div
                            class="w-12 h-6 transition rounded-full shadow-md outline-none"
                            :class="[
                                _form.auto_renew
                                    ? 'bg-blue-500'
                                    : 'bg-gray-500',
                            ]"
                        ></div>

                        <div
                            class="absolute inline-flex items-center justify-center w-4 h-4 transition-all duration-200 ease-in-out transform bg-white rounded-full shadow-sm top-1 left-1"
                            :class="{
                                'translate-x-0 rtl:translate-x-6':
                                    !_form.auto_renew,
                                'translate-x-6 rtl:translate-x-0':
                                    _form.auto_renew,
                            }"
                        ></div>
                    </button>
                    <span class="text-sm font-medium">
                        {{ __("On") }}
                    </span>
                </div>
            </div>

            <div
                class="flex items-center justify-end relative ltr:ml-auto rtl:mr-auto"
                v-if="
                    apartment.state.toLowerCase() == 'empty' ||
                    apartment.state.toLowerCase() == 'reserved'
                "
            >
                <button
                    type="submit"
                    class="text-white bg-blue-700 focus:outline-none font-medium text-sm px-5 py-2.5 flex items-center justify-center space-x-2 rtl:space-x-reverse ltr:rounded-l-lg rtl:rounded-r-lg"
                    :class="[
                        _form.processing
                            ? 'bg-opacity-50'
                            : 'hover:bg-blue-800',
                    ]"
                    @click.prevent="_submitHandler($event, true)"
                    :disabled="_form.processing"
                >
                    <Loader v-if="_form.processing && !_form.checkin_now" />
                    <span>{{ __("Check-in Reservation") }}</span>
                </button>

                <button
                    type="button"
                    @click.stop="
                        if (!_form.processing)
                            isSubmitButtonDropdownOpen =
                                !isSubmitButtonDropdownOpen;
                    "
                    :class="[
                        _form.processing
                            ? 'bg-opacity-50'
                            : 'hover:bg-blue-800',
                    ]"
                    :disabled="_form.processing"
                    class="text-white bg-blue-700 focus:outline-none font-medium text-sm px-3.5 py-2.5 space-x-2 rtl:space-x-reverse ltr:rounded-r-lg rtl:rounded-l-lg ltr:border-l rtl:border-r no-outside-click"
                >
                    <FontAwesomeIcon
                        icon="fas fa-caret-down"
                        class="no-outside-click"
                    />
                </button>

                <div
                    v-if="isSubmitButtonDropdownOpen"
                    v-on-click-outside="closeSubmitButtonDropdown"
                    class="absolute top-12 ltr:left-3/4 rtl:right-3/4 py-2 w-48 bg-white rounded-md shadow-xl border-2 z-50"
                >
                    <button
                        type="submit"
                        @click.prevent="_submitHandler($event, false)"
                        class="px-4 py-2 text-sm capitalize text-gray-900 w-full flex justify-start items-center space-x-2 rtl:space-x-reverse"
                        :class="[
                            _form.processing
                                ? 'bg-opacity-50'
                                : 'hover:bg-gray-300',
                        ]"
                    >
                        <span>{{ __("Add The Reservation") }}</span>
                        <Loader v-if="_form.processing && _form.checkin_now" />
                    </button>
                </div>
            </div>

            <div
                class="flex items-center justify-end ltr:ml-auto rtl:mr-auto"
                v-else
            >
                <button
                    type="submit"
                    class="text-white bg-blue-700 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 flex items-center justify-center space-x-2 rtl:space-x-reverse"
                    :class="[
                        _form.processing
                            ? 'bg-opacity-50'
                            : 'hover:bg-blue-800',
                    ]"
                    @click.prevent="_submitHandler($event, false)"
                    :disabled="_form.processing"
                >
                    <Loader v-if="_form.processing" />
                    <span>{{ __("Add The Reservation") }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<style>
[dir="rtl"] .vti__dropdown-list {
    right: 0 !important;
}

[dir="rtl"] .vti__dropdown-item {
    text-align: right !important;
    unicode-bidi: plaintext !important;
}

[dir="rtl"] .vti__input {
    direction: ltr !important;
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

[dir="rtl"] .vti__dropdown-item {
    direction: ltr !important;
    text-align: left !important;
}
</style>

<style>
[dir="ltr"] #option-1:checked ~ div {
    --tw-translate-x: 0%;
}

[dir="ltr"] #option-2:checked ~ div {
    --tw-translate-x: 100%;
}

[dir="ltr"] #option-3:checked ~ div {
    --tw-translate-x: 200%;
}

[dir="rtl"] #option-1:checked ~ div {
    --tw-translate-x: -0%;
}

[dir="rtl"] #option-2:checked ~ div {
    --tw-translate-x: -100%;
}

[dir="rtl"] #option-3:checked ~ div {
    --tw-translate-x: -200%;
}
</style>
