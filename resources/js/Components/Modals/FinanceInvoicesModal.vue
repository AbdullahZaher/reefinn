<script setup>
import { nextTick, ref, watch } from "vue";
import Modal from "@/Components/Modals/Modal.vue";
import Loader from "@/Components/Loader.vue";
import PrintModal from "@/Components/Modals/PrintModal.vue";
import { __ } from "@/Composables/translations";
import { debounce } from "lodash";

const props = defineProps({
    open: {
        type: Boolean,
    },
});

const emit = defineEmits(["close"]);

const isLoading = ref(false);

const searchQuery = ref("");
const searchInput = ref(null);
const isError = ref(false);

const reservations = ref([]);
const selectedReservation = ref(null);

const search = debounce(() => {
    axios
        .post(route("reservations.search", { query: searchQuery.value }))
        .then((response) => {
            reservations.value = response.data.data;
        })
        .finally(() => {
            isLoading.value = false;
        });
}, 600);

watch(searchQuery, (value) => {
    if (selectedReservation.value) {
        selectedReservation.value = null;
        searchQuery.value = "";
    }

    if (value.replaceAll(" ", "").length) {
        isLoading.value = true;
        search();
    }
});

const selectReservation = async (reservation) => {
    isError.value = false;
    searchInput.value.blur();
    searchQuery.value = `${reservation.apartment.name} - ${reservation.guest_name} - #${reservation.reservation_number}`;
    await nextTick();
    selectedReservation.value = reservation;
};

const isPrintModalOpen = ref(false);
const printModalUrl = ref("");
const printModalWindowTitle = ref("");

const printReceiptVoucher = () => {
    isError.value = false;

    if (selectedReservation?.value?.id) {
        printModalUrl.value = route("reservations.receipt", {
            reservation: selectedReservation.value.id,
        });
        printModalWindowTitle.value = __("Receipt Voucher");
        isPrintModalOpen.value = true;
    } else {
        isError.value = true;
    }
};

const printTaxInvoice = () => {
    isError.value = false;

    if (selectedReservation?.value?.id) {
        printModalUrl.value = route("reservations.tax", {
            reservation: selectedReservation.value.id,
        });
        printModalWindowTitle.value = __("Tax Invoice");
        isPrintModalOpen.value = true;
    } else {
        isError.value = true;
    }
};
</script>

<template>
    <PrintModal
        :routeUrl="printModalUrl"
        :windowTitle="printModalWindowTitle"
        :open="isPrintModalOpen"
        @close="isPrintModalOpen = false"
        v-if="isPrintModalOpen"
    />

    <Modal
        :headerTitle="__('Invoices')"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="!isPrintModalOpen"
    >
        <div class="mb-3">
            <input
                type="text"
                :placeholder="__('Search for a reservation...')"
                class="w-full text-xl rounded-lg focus:outline-none focus:ring-0 focus:border-blue-300 transition-all duration-100 ease-in-out border-gray-400"
                v-model="searchQuery"
                ref="searchInput"
            />

            <p class="text-red-600 text-sm mt-2" v-if="isError">
                {{ __("Please select a reservation") }}
            </p>
        </div>

        <div
            class="bg-white w-full rounded-xl shadow-2xl overflow-hidden py-1 border"
            v-if="
                searchQuery.replaceAll(' ', '').length && !selectedReservation
            "
        >
            <div class="flex items-center justify-center py-4" v-if="isLoading">
                <Loader large />
            </div>

            <template v-else>
                <div
                    class="w-full flex p-3 ltr:pl-4 rtl:pr-4 items-center hover:bg-gray-200 transition-all duration-150 ease-in-out cursor-pointer"
                    @click="selectReservation(reservation)"
                    v-for="reservation in reservations"
                    :key="reservation.id"
                >
                    <div class="ltr:mr-4 rtl:ml-4">
                        <div
                            class="w-12 h-12 p-2 rounded-sm text-base font-bold text-white select-none text-center flex items-center justify-center"
                            :class="`bg-red-400`"
                        >
                            <span>{{ reservation.apartment.name }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-lg">
                            {{ __("Guest Name") }}: {{ reservation.guest_name }}
                        </div>
                        <div
                            class="text-xs text-gray-500 flex items-center flex-wrap"
                        >
                            <span class="ltr:mr-2 rtl:ml-2 whitespace-nowrap">
                                {{ __("Reservation Number") }}:
                                {{ reservation.reservation_number }}
                            </span>
                            <span class="ltr:mr-2 rtl:ml-2 whitespace-nowrap">
                                {{ __("Check-in") }}: {{ reservation.checkin }}
                            </span>
                            <span class="ltr:mr-2 rtl:ml-2 whitespace-nowrap">
                                {{ __("Check-out") }}:
                                {{ reservation.checkout }}
                            </span>
                            <span class="ltr:mr-2 rtl:ml-2 whitespace-nowrap">
                                {{ __("Total") }}: {{ reservation.total_price }}
                                {{ __("SAR") }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    class="text-center py-4 font-semibold"
                    v-if="reservations.length === 0"
                >
                    {{ __("No reservations was found.") }}
                </div>
            </template>
        </div>

        <div class="mt-6 flex items-center gap-5">
            <button
                type="button"
                class="bg-blue-600 py-2 flex-1 text-white rounded-lg font-medium focus:outline-none focus:ring-0 focus:border-blue-300 transition-all duration-100 ease-in-out flex items-center justify-center gap-2"
                @click="printReceiptVoucher"
            >
                <FontAwesomeIcon icon="fas fa-print" />
                <span>{{ __("Print Receipt Voucher") }}</span>
            </button>
            <button
                type="button"
                class="bg-blue-600 py-2 flex-1 text-white rounded-lg font-medium focus:outline-none focus:ring-0 focus:border-blue-300 transition-all duration-100 ease-in-out flex items-center justify-center gap-2"
                @click="printTaxInvoice"
            >
                <FontAwesomeIcon icon="fas fa-print" />
                <span>{{ __("Print Tax Invoice") }}</span>
            </button>
        </div>
    </Modal>
</template>
