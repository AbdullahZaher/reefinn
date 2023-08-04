<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import ShowApartmentDetailsModal from "@/Components/Modals/ShowApartmentDetailsModal.vue";
import ShowApartmentRecordsModal from "@/Components/Modals/ShowApartmentRecordsModal.vue";
import ShowReservationsModal from "@/Components/Modals/ShowReservationsModal.vue";
import EditReservationModal from "@/Components/Modals/EditReservationModal.vue";
import SubmitCleaningModal from "@/Components/Modals/SumbitCleaningModal.vue";
import PrintModal from "@/Components/Modals/PrintModal.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    apartment: {
        type: Object,
    },
});

const isShowApartmentDetailsModalOpen = ref(false);
const isShowApartmentRecordsModalOpen = ref(false);
const isShowReservationsModalOpen = ref(false);
const isEditReservationModalOpen = ref(false);
const isPrintModalOpen = ref(false);
const isSubmitCleaningModalOpen = ref(false);

const changeState = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, update it!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.patch(
                    route("apartments.update.state", props.apartment.id),
                    {},
                    {
                        preserveScroll: true,
                        onFinish: resolve,
                    }
                );
            }),
    });
};

const updateMaintenance = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, change it!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.patch(
                    route("apartments.update.maintenance", props.apartment.id),
                    {},
                    {
                        preserveScroll: true,
                        onSuccess: resolve,
                    }
                );
            }),
    });
};
</script>

<template>
    <div
        class="w-full rounded-lg p-12 flex flex-col justify-center items-center relative"
        :class="`bg-${apartment.state_color}-400`"
    >
        <button
            type="click"
            class="absolute top-0 p-3"
            :class="[$page.props.locale.dir == 'ltr' ? 'right-0' : 'left-0']"
            @click="isShowApartmentDetailsModalOpen = true"
            v-if="$page.props.auth.user.can.includes('show apartments')"
        >
            <FontAwesomeIcon
                icon="fas fa-circle-info"
                class="text-white text-2xl"
            />
        </button>

        <button
            type="click"
            class="absolute top-0 p-3"
            :class="[$page.props.locale.dir == 'ltr' ? 'left-0' : 'right-0']"
            @click="isShowApartmentRecordsModalOpen = true"
            v-if="$page.props.auth.user.can.includes('show records')"
        >
            <FontAwesomeIcon
                icon="fas fa-clock-rotate-left"
                class="text-white text-2xl"
            />
        </button>

        <ShowApartmentDetailsModal
            :apartment="apartment"
            :open="isShowApartmentDetailsModalOpen"
            @close="isShowApartmentDetailsModalOpen = false"
            v-if="
                isShowApartmentDetailsModalOpen &&
                $page.props.auth.user.can.includes('show apartments')
            "
        />

        <ShowApartmentRecordsModal
            :apartment="apartment"
            :open="isShowApartmentRecordsModalOpen"
            @close="isShowApartmentRecordsModalOpen = false"
            v-if="
                isShowApartmentRecordsModalOpen &&
                $page.props.auth.user.can.includes('show records')
            "
        />

        <div class="text-center space-y-6">
            <div class="space-y-2 select-none">
                <h3 class="text-6xl text-white font-bold break-all">
                    {{ apartment.name }}
                </h3>
                <p class="text-xl text-slate-100 font-normal">
                    {{ __(apartment.state) }}
                    <span
                        class="text-base block text-slate-200"
                        v-if="apartment.state.toLowerCase() == 'inhabited'"
                    >
                        {{ `(${apartment.days_left} ${__("days left")})` }}
                    </span>
                </p>
            </div>
            <div
                class="flex justify-center items-center space-x-5 rtl:space-x-reverse"
            >
                <ShowReservationsModal
                    :apartment="apartment"
                    :open="isShowReservationsModalOpen"
                    @close="isShowReservationsModalOpen = false"
                    v-if="
                        isShowReservationsModalOpen &&
                        $page.props.auth.user.can.includes(
                            'show reservations'
                        ) &&
                        apartment.state.toLowerCase() != 'maintenance'
                    "
                />

                <EditReservationModal
                    :apartment="apartment"
                    :reservation="apartment.current_reservation"
                    :open="isEditReservationModalOpen"
                    @close="isEditReservationModalOpen = false"
                    v-if="
                        isEditReservationModalOpen &&
                        $page.props.auth.user.can.includes('edit reservations')
                    "
                />

                <PrintModal
                    :routeUrl="
                        route(
                            'reservations.invoice',
                            apartment.current_reservation.id
                        )
                    "
                    :windowTitle="`${__('Invoice for reservation')} ${
                        apartment.current_reservation.id
                    } `"
                    :open="isPrintModalOpen"
                    @close="isPrintModalOpen = false"
                    v-if="
                        isPrintModalOpen &&
                        $page.props.auth.user.can.includes('print reservations')
                    "
                />

                <SubmitCleaningModal
                    :apartment="apartment"
                    :open="isSubmitCleaningModalOpen"
                    @close="isSubmitCleaningModalOpen = false"
                    v-if="
                        isSubmitCleaningModalOpen &&
                        $page.props.auth.user.can.includes('empty apartments')
                    "
                />

                <button
                    type="button"
                    :title="__('Reservations')"
                    @click="isShowReservationsModalOpen = true"
                    v-if="
                        $page.props.auth.user.can.includes(
                            'show reservations'
                        ) && apartment.state.toLowerCase() != 'maintenance'
                    "
                >
                    <FontAwesomeIcon
                        icon="fas fa-calendar"
                        class="text-white text-2xl"
                    />
                </button>

                <template v-if="apartment.state.toLowerCase() == 'inhabited'">
                    <button
                        type="button"
                        :title="__('Edit')"
                        @click="isEditReservationModalOpen = true"
                        v-if="
                            $page.props.auth.user.can.includes(
                                'edit reservations'
                            )
                        "
                    >
                        <FontAwesomeIcon
                            icon="fas fa-pen-to-square"
                            class="text-white text-2xl"
                        />
                    </button>
                    <button
                        type="button"
                        :title="__('Check-out')"
                        @click="changeState"
                        v-if="
                            $page.props.auth.user.can.includes(
                                'checkout apartments'
                            )
                        "
                    >
                        <FontAwesomeIcon
                            icon="fas fa-person-walking-arrow-right"
                            class="text-white text-2xl"
                        />
                    </button>

                    <button
                        type="button"
                        :title="__('Print')"
                        @click="isPrintModalOpen = true"
                        v-if="
                            $page.props.auth.user.can.includes(
                                'print reservations'
                            )
                        "
                    >
                        <FontAwesomeIcon
                            icon="fas fa-print"
                            class="text-white text-2xl"
                        />
                    </button>
                </template>

                <template
                    v-else-if="apartment.state.toLowerCase() == 'cleaning'"
                >
                    <button
                        type="button"
                        :title="__('Change to empty')"
                        @click="isSubmitCleaningModalOpen = true"
                        v-if="
                            $page.props.auth.user.can.includes(
                                'empty apartments'
                            )
                        "
                    >
                        <FontAwesomeIcon
                            icon="fas fa-clipboard-check"
                            class="text-white text-2xl"
                        />
                    </button>
                </template>

                <template
                    v-else-if="apartment.state.toLowerCase() == 'maintenance'"
                >
                    <button
                        @click="updateMaintenance"
                        :title="__('Finish Maintenance')"
                        v-if="
                            $page.props.auth.user.can.includes(
                                'maintenance apartments'
                            )
                        "
                    >
                        <FontAwesomeIcon
                            icon="fas fa-unlock"
                            class="text-white text-2xl"
                        />
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>
