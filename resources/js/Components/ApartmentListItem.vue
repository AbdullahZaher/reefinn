<script setup>
import { ref } from "vue";
import { router, Link } from "@inertiajs/vue3";
import ShowApartmentDetailsModal from "@/Components/Modals/ShowApartmentDetailsModal.vue";
import ShowApartmentRecordsModal from "@/Components/Modals/ShowApartmentRecordsModal.vue";
import ShowReservationsModal from "@/Components/Modals/ShowReservationsModal.vue";
import EditReservationModal from "@/Components/Modals/EditReservationModal.vue";
import CheckoutReservationModal from "@/Components/Modals/CheckoutReservationModal.vue";
import SubmitCleaningModal from "@/Components/Modals/SumbitCleaningModal.vue";
import PrintModal from "@/Components/Modals/PrintModal.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    apartment: {
        type: Object,
    },
    idTypes: {
        type: Object,
    },
    paymentMethods: {
        type: Object,
    },
});

const isShowApartmentDetailsModalOpen = ref(false);
const isShowApartmentRecordsModalOpen = ref(false);
const isShowReservationsModalOpen = ref(false);
const isEditReservationModalOpen = ref(false);
const isPrintModalOpen = ref(false);
const isCheckoutReservationModalOpen = ref(false);
const isSubmitCleaningModalOpen = ref(false);

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
    <tr
        tabindex="0"
        class="focus:outline-none text-sm leading-none text-gray-600 h-16 border bg-white rounded-2xl"
    >
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

        <ShowReservationsModal
            :apartment="apartment"
            :idTypes="idTypes"
            :paymentMethods="paymentMethods"
            :open="isShowReservationsModalOpen"
            @close="isShowReservationsModalOpen = false"
            v-if="
                isShowReservationsModalOpen &&
                $page.props.auth.user.can.includes('show reservations') &&
                apartment.state.toLowerCase() != 'maintenance'
            "
        />

        <EditReservationModal
            :apartment="apartment"
            :reservation="apartment.current_reservation"
            :idTypes="idTypes"
            :paymentMethods="paymentMethods"
            :open="isEditReservationModalOpen"
            @close="isEditReservationModalOpen = false"
            v-if="
                isEditReservationModalOpen &&
                $page.props.auth.user.can.includes('edit reservations')
            "
        />

        <PrintModal
            :routeUrl="
                route('reservations.invoice', apartment.current_reservation.id)
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

        <CheckoutReservationModal
            :apartment="apartment"
            :open="isCheckoutReservationModalOpen"
            @close="isCheckoutReservationModalOpen = false"
            v-if="
                isCheckoutReservationModalOpen &&
                $page.props.auth.user.can.includes('checkout apartments')
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

        <td class="w-1/4 ltr:pl-4 rtl:pr-4">
            <div class="flex items-center gap-5">
                <div
                    class="min-w-[3rem] h-12 p-2 rounded-sm text-base font-bold text-white select-none text-center flex items-center justify-center"
                    :class="`bg-${apartment.state_color}-400`"
                >
                    <span>{{ apartment.name }}</span>
                </div>
                <div class="ltr:pl-2 ltr:pr-2">
                    <p class="text-sm font-medium leading-none text-gray-800">
                        {{ __(apartment.type) }}
                    </p>
                    <p class="text-xs leading-3 text-gray-600 mt-2">
                        {{ __("State") }}: {{ __(apartment.state) }}
                        <span
                            class="text-xs leading-3 text-gray-600 mt-2"
                            v-if="apartment.state.toLowerCase() == 'reserved'"
                        >
                            ({{
                                __("Count") +
                                ": " +
                                apartment.reservations.length
                            }})
                        </span>
                        <span
                            class="text-xs leading-3 text-gray-600 mt-2"
                            v-if="apartment.state.toLowerCase() == 'inhabited'"
                        >
                            {{ `(${apartment.days_left} ${__("days left")})` }}
                        </span>
                    </p>
                </div>
            </div>
        </td>
        <td class="px-8 text-center">
            <p>{{ apartment.price_for_night + " " + __("SAR") }}</p>
        </td>
        <td>
            <p class="px-8 text-center" v-if="apartment.current_reservation">
                {{ apartment.current_reservation.checkin }}
            </p>
            <p class="px-8 text-center" v-else>-</p>
        </td>
        <td>
            <p class="px-8 text-center" v-if="apartment.current_reservation">
                {{ apartment.current_reservation.checkout }}
            </p>
            <p class="px-8 text-center" v-else>-</p>
        </td>
        <td>
            <p class="px-8 text-center"></p>
            <p class="px-8 text-center" v-if="apartment.current_reservation">
                {{
                    apartment.current_reservation.amounts_due + " " + __("SAR")
                }}
            </p>
            <p class="px-8 text-center" v-else>-</p>
        </td>

        <td class="px-8">
            <div class="mx-auto space-x-5 rtl:space-x-reverse w-fit">
                <button
                    type="click"
                    :title="__('Show Records')"
                    @click="isShowApartmentRecordsModalOpen = true"
                    v-if="$page.props.auth.user.can.includes('show records')"
                >
                    <FontAwesomeIcon
                        icon="fas fa-clock-rotate-left"
                        class="text-sm"
                    />
                </button>
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
                    <FontAwesomeIcon icon="fas fa-table" class="text-sm" />
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
                            class="text-sm"
                        />
                    </button>
                    <button
                        type="button"
                        :title="__('Check-out')"
                        @click="isCheckoutReservationModalOpen = true"
                        v-if="
                            $page.props.auth.user.can.includes(
                                'checkout apartments'
                            )
                        "
                    >
                        <FontAwesomeIcon
                            icon="fas fa-person-walking-arrow-right"
                            class="text-sm"
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
                        <FontAwesomeIcon icon="fas fa-print" class="text-sm" />
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
                            class="text-sm"
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
                        <FontAwesomeIcon icon="fas fa-unlock" class="text-sm" />
                    </button>
                </template>

                <Link
                    :href="
                        route('calendar.index', {
                            apartments: apartment.id,
                            only_one: true,
                        })
                    "
                    :title="__('Show reservations calendar')"
                    v-if="
                        $page.props.auth.user.can.includes('show calendar') &&
                        apartment.state.toLowerCase() != 'maintenance'
                    "
                >
                    <FontAwesomeIcon
                        icon="fas fa-calendar-days"
                        class="text-sm"
                    />
                </Link>

                <button
                    type="click"
                    :title="__('Show Apartment Details')"
                    @click="isShowApartmentDetailsModalOpen = true"
                    v-if="$page.props.auth.user.can.includes('show apartments')"
                >
                    <FontAwesomeIcon
                        icon="fas fa-circle-info"
                        class="text-sm"
                    />
                </button>
            </div>
        </td>
    </tr>
</template>
