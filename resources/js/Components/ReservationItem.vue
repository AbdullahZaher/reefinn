<script setup>
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import ShowGuestInfo from "@/Components/Modals/ShowGuestInfo.vue";
import EditReservationModal from "@/Components/Modals/EditReservationModal.vue";
import PrintModal from "@/Components/Modals/PrintModal.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    reservation: {
        type: Object,
    },
    reservationTypes: {
        type: Object,
    },
    idTypes: {
        type: Object,
    },
    paymentMethods: {
        type: Object,
    },
});

const isShowGuestInfoModalOpen = ref(false);
const isEditReservationModalOpen = ref(false);
const isPrintModalOpen = ref(false);

const cancelReservation = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, cancel reservation!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.delete(
                    route("reservations.cancel", props.reservation.id),
                    {
                        preserveScroll: true,
                        onFinish: resolve,
                    }
                );
            }),
    });
};

const deleteReservation = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, delete reservation!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.delete(
                    route("reservations.destroy", props.reservation.id),
                    {
                        preserveScroll: true,
                        onFinish: resolve,
                    }
                );
            }),
    });
};
</script>

<template>
    <ShowGuestInfo
        :reservation="reservation"
        :open="isShowGuestInfoModalOpen"
        @close="isShowGuestInfoModalOpen = false"
        v-if="isShowGuestInfoModalOpen && reservation.guest_name"
    />

    <EditReservationModal
        :apartment="reservation.apartment"
        :reservation="reservation"
        :types="reservationTypes"
        :idTypes="idTypes"
        :paymentMethods="paymentMethods"
        :open="isEditReservationModalOpen"
        @close="isEditReservationModalOpen = false"
        v-if="
            isEditReservationModalOpen &&
            (reservation.state.toLowerCase() == 'pending' ||
                reservation.state.toLowerCase() == 'checkin') &&
            $page.props.auth.user.can.includes('edit reservations')
        "
    />

    <PrintModal
        :routeUrl="route('reservations.invoice', reservation.id)"
        :windowTitle="`${__('Invoice for reservation')} ${
            props.reservation.id
        } `"
        :open="isPrintModalOpen"
        @close="isPrintModalOpen = false"
        v-if="
            isPrintModalOpen &&
            $page.props.auth.user.can.includes('print reservations')
        "
    />

    <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
        >
            {{ reservation.reservation_number }}
        </th>
        <td class="px-6 py-4 flex items-center justify-center">
            <span
                class="px-3 py-1 rounded-xl text-white whitespace-nowrap"
                :class="`bg-${reservation.state_color}-500`"
            >
                {{ __(reservation.state) }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.checkin }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.checkout }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.apartment.name }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.price_for_night + " " + __("SAR") }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap space-x-1 rtl:space-x-reverse">
            <template v-if="parseFloat(reservation.taxes_amount)">
                <span>{{ reservation.taxes_amount + " " + __("SAR") }}</span>
                <span class="text-xs"
                    >({{ reservation.taxes_percentage }}%)</span
                >
            </template>
            <span v-else>-</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.total_price + " " + __("SAR") }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <template v-if="parseFloat(reservation.discount)">
                <span class="font-medium">
                    {{ reservation.discount + "%" }}
                </span>
                <span class="text-xs">
                    ({{ reservation.discount_amount + " " + __("SAR") }})
                </span>
            </template>
            <span v-else>-</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.amounts_due + " " + __("SAR") }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ __(reservation.payment_method) }}
        </td>
        <td class="px-6 py-4 text-right whitespace-nowrap">
            <button
                type="button"
                class="font-medium text-blue-600 underline whitespace-nowrap"
                :title="__('Show Guest Details')"
                @click="isShowGuestInfoModalOpen = true"
                v-if="reservation.guest_name"
            >
                {{ reservation.guest_name }}
            </button>
            <span v-else>-</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.admin_name ?? "-" }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ reservation.created_at }}
        </td>
        <td
            class="px-6 py-4 flex items-center justify-center gap-5 whitespace-nowrap"
        >
            <button
                @click="isEditReservationModalOpen = true"
                :title="__('Edit Reservation')"
                v-if="
                    $page.props.auth.user.can.includes('edit reservations') &&
                    (reservation.state.toLowerCase() == 'pending' ||
                        reservation.state.toLowerCase() == 'checkin')
                "
            >
                <FontAwesomeIcon icon="fas fa-pen-to-square" />
            </button>

            <button
                @click="cancelReservation"
                :title="__('Cancel Reservation')"
                v-if="
                    $page.props.auth.user.can.includes('cancel reservations') &&
                    reservation.state.toLowerCase() == 'pending'
                "
            >
                <FontAwesomeIcon icon="fas fa-ban" />
            </button>

            <button
                @click="isPrintModalOpen = true"
                :title="__('Print Reservation')"
                v-if="$page.props.auth.user.can.includes('print reservations')"
            >
                <FontAwesomeIcon icon="fas fa-print" />
            </button>

            <button
                @click="deleteReservation"
                :title="__('Delete Reservation')"
                v-if="
                    $page.props.auth.user.can.includes('delete reservations') &&
                    reservation.state.toLowerCase() != 'checkin'
                "
            >
                <FontAwesomeIcon icon="fas fa-trash" />
            </button>

            <span
                v-if="
                    !$page.props.auth.user.can.includes('print reservations') &&
                    !$page.props.auth.user.can.includes('delete reservations')
                "
                >-</span
            >
        </td>
    </tr>
</template>
