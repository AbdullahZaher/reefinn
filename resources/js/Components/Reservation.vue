<script setup>
import { ref, watch, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import EditReservationModal from "@/Components/Modals/EditReservationModal.vue";
import PrintModal from "@/Components/Modals/PrintModal.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    apartment: {
        type: Object,
    },
    reservation: {
        type: Object,
    },
    clickOutsideToClose: {
        type: Boolean,
    },
});

const emit = defineEmits(["update:clickOutsideToClose", "close"]);

const isEditReservationModalOpen = ref(false);
const isPrintModalOpen = ref(false);

const checkingIn = ref(false);

watch(isEditReservationModalOpen, (value) =>
    emit("update:clickOutsideToClose", !value)
);

watch(isPrintModalOpen, (value) => emit("update:clickOutsideToClose", !value));

const checkin = () => {
    emit("update:clickOutsideToClose", false);
    checkingIn.value = true;

    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, check-in!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.patch(
                    route("reservations.checkin", props.reservation.id),
                    {},
                    {
                        preserveScroll: true,
                        onFinish: resolve,
                    }
                );
            }),
    });
};

const cancelReservation = () => {
    emit("update:clickOutsideToClose", false);

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

onUnmounted(() => {
    emit("update:clickOutsideToClose", true);
    if (checkingIn.value) emit("close");
});
</script>

<template>
    <div class="text-center space-y-6">
        <div class="space-y-2 select-none">
            <h3 class="text-2xl text-white font-bold break-all">
                {{ reservation.guest_name }}
            </h3>
            <p class="text-base block text-slate-100 font-normal">
                {{ `(${__("Check-in At")} ${reservation.checkin})` }}
            </p>
            <p class="text-base block text-slate-100 font-normal">
                {{ `(${__("Check-out At")} ${reservation.checkout})` }}
            </p>
        </div>
        <div
            class="flex justify-center items-center space-x-5 rtl:space-x-reverse"
        >
            <EditReservationModal
                :apartment="apartment"
                :reservation="reservation"
                :open="isEditReservationModalOpen"
                @close="isEditReservationModalOpen = false"
                v-if="
                    isEditReservationModalOpen &&
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

            <button
                type="button"
                :title="__('Edit')"
                @click="isEditReservationModalOpen = true"
                v-if="$page.props.auth.user.can.includes('edit reservations')"
            >
                <FontAwesomeIcon
                    icon="fas fa-pen-to-square"
                    class="text-white text-2xl"
                />
            </button>

            <button
                type="button"
                :title="__('Check-in')"
                @click="checkin"
                v-if="
                    apartment.state.toLowerCase() == 'reserved' &&
                    $page.props.auth.user.can.includes('checkin apartments')
                "
            >
                <FontAwesomeIcon
                    icon="fas fa-right-from-bracket"
                    class="text-white text-2xl"
                />
            </button>

            <button
                type="button"
                :title="__('Cancel Reservation')"
                @click="cancelReservation"
                v-if="$page.props.auth.user.can.includes('cancel reservations')"
            >
                <FontAwesomeIcon
                    icon="fas fa-ban"
                    class="text-white text-2xl"
                />
            </button>

            <button
                type="button"
                :title="__('Print')"
                @click="isPrintModalOpen = true"
                v-if="$page.props.auth.user.can.includes('print reservations')"
            >
                <FontAwesomeIcon
                    icon="fas fa-print"
                    class="text-white text-2xl"
                />
            </button>
        </div>
    </div>
</template>
