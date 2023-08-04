<script setup>
import { __ } from "@/Composables/translations";

const props = defineProps({
    counts: {
        type: Object,
    },
    reservationStateColors: {
        type: Object,
    },
});
</script>

<template>
    <div
        class="flex flex-col lg:flex-row w-full lg:space-x-8 rtl:lg:space-x-reverse space-y-8 lg:space-y-0 mb-2 lg:mb-4 px-4 justify-center"
    >
        <div class="w-full lg:w-1/3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4 border-blue-400"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 bg-blue-400 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-calendar"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.reservations }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Reservations") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="flex flex-col lg:flex-row space-y-8 lg:space-y-0 mb-2 lg:mb-4 mt-24 tree relative"
    >
        <div class="w-full lg:w-1/4 tree-element px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4"
                :class="`border-${reservationStateColors.pending}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${reservationStateColors.pending}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-calendar"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.pending_reservations }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Pending Reservations") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/4 tree-element px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4"
                :class="`border-${reservationStateColors.checkin}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${reservationStateColors.checkin}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-calendar"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.checkin_reservations }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Checkin Reservations") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/4 tree-element px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4"
                :class="`border-${reservationStateColors.checkout}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${reservationStateColors.checkout}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-calendar"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.checkout_reservations }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Checkout Reservations") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/4 tree-element px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4"
                :class="`border-${reservationStateColors.canceled}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${reservationStateColors.canceled}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-calendar"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.canceled_reservations }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Canceled Reservations") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
