<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import ApartmentsTree from "@/Components/Statistics/ApartmentsTree.vue";
import ReservationsTree from "@/Components/Statistics/ReservationsTree.vue";
import MoneyInfoCards from "@/Components/Statistics/MoneyInfoCards.vue";
import TopReservedApartments from "@/Components/Statistics/TopReservedApartments.vue";
import { __ } from "@/Composables/translations";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps({
    filters: {
        type: Object,
    },
    months: {
        type: Array,
    },
    counts: {
        type: Object,
    },
    apartments: {
        type: Array,
    },
    apartmentStateColors: {
        type: Object,
    },
    reservationStateColors: {
        type: Object,
    },
});

const isTimeMenuOpen = ref(false);
const activeTime = ref(props.filters.time);

watch(
    () => activeTime.value.key,
    (newValue) => {
        router.reload({
            data: {
                time: newValue,
            },
            only: ["counts", "apartments"],
        });
    }
);
</script>

<template>
    <Head :title="__('Statistics')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Statistics") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-[100rem] mx-auto sm:px-6 lg:px-8">
                <div class="px-4 sm:px-6 lg:px-4 py-12">
                    <div
                        class="text-center pb-12 flex items-center justify-between flex-col lg:flex-row space-y-6 lg:space-y-0"
                    >
                        <h1
                            class="font-bold text-5xl font-heading text-gray-900"
                        >
                            {{ __("Statistics") }}
                        </h1>
                    </div>
                </div>

                <!-- Apartments Count -->
                <div class="pb-24">
                    <ApartmentsTree
                        :counts="counts"
                        :apartmentStateColors="apartmentStateColors"
                    />
                </div>

                <div
                    class="border-2 divide-y-2 px-10 border-gray-400 rounded-2xl"
                >
                    <!-- Menu -->
                    <div class="w-full py-16">
                        <div class="w-fit mx-auto">
                            <div
                                class="bg-white rounded-lg shadow-xl flex items-center px-4 py-2 cursor-pointer"
                                @click="isTimeMenuOpen = !isTimeMenuOpen"
                            >
                                <input
                                    type="text"
                                    :placeholder="__(activeTime.value)"
                                    :value="activeTime.value"
                                    readonly
                                    class="pointer-events-none text-base placeholder-gray-6400 outline-none w-full h-full flex-1 border-none text-center"
                                />
                                <FontAwesomeIcon icon="fas fa-caret-down" />
                            </div>
                            <svg
                                class="absolute -bottom-2/3 left-1/2 transform -translate-x-1/2"
                                width="30"
                                height="20"
                                viewBox="0 0 30 20"
                                xmlns="http://www.w3.org/2000/svg"
                                v-if="isTimeMenuOpen"
                            >
                                <polygon
                                    points="15, 0 30, 20 0, 20"
                                    fill="#FFFFFF"
                                />
                            </svg>
                            <div
                                class="bg-white rounded-lg shadow-xl absolute left-1/2 transform -translate-x-1/2 mt-8 z-50 w-64 h-[30vh] overflow-y-scroll space-y-2"
                                v-if="isTimeMenuOpen"
                            >
                                <div
                                    class="py-4 px-16 transition duration-150 ease-in-out cursor-pointer text-center"
                                    :class="[
                                        activeTime.key == 'all_time'
                                            ? 'bg-gray-100'
                                            : 'hover:bg-gray-100',
                                    ]"
                                    @click="
                                        activeTime.key = 'all_time';
                                        activeTime.value = __('All Time');
                                        isTimeMenuOpen = false;
                                    "
                                >
                                    {{ __("All Time") }}
                                </div>
                                <div
                                    class="py-4 px-16 transition duration-150 ease-in-out cursor-pointer text-center"
                                    v-for="(month, index) in months"
                                    :key="index"
                                    :class="[
                                        activeTime.key == month.key
                                            ? 'bg-gray-100'
                                            : 'hover:bg-gray-100',
                                    ]"
                                    @click="
                                        activeTime.key = month.key;
                                        activeTime.value = month.value;
                                        isTimeMenuOpen = false;
                                    "
                                >
                                    {{ month.value }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reservations -->
                    <div class="py-16">
                        <ReservationsTree
                            :counts="counts"
                            :reservationStateColors="reservationStateColors"
                        />
                    </div>

                    <!-- Info -->
                    <div class="py-16">
                        <MoneyInfoCards :counts="counts" />
                    </div>

                    <!-- Top Reserved Apartments -->
                    <div class="py-16">
                        <TopReservedApartments :apartments="apartments" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.tree::before {
    content: "";
    position: absolute;
    top: -16%;
    left: 50%;
    border-left: 2px solid #ccc;
    width: 0;
    height: 45px;
}

@media (min-width: 1024px) {
    .tree::before {
        content: "";
        position: absolute;
        top: -100%;
        left: 50%;
        border-left: 2px solid #ccc;
        width: 0;
        height: 45px;
    }

    .tree-element {
        position: relative;
    }

    .tree-element::before,
    .tree-element::after {
        content: "";
        position: absolute;
        top: -50%;
        right: 50%;
        border-top: 2px solid #ccc;
        width: 50%;
        height: 18px;
    }

    .tree-element::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #ccc;
    }

    .tree-element:only-child::after,
    .tree-element:only-child::before {
        display: none;
    }

    .tree-element:only-child {
        padding-top: 0;
    }

    :is([dir="ltr"]) .tree-element:first-child::before,
    :is([dir="ltr"]) .tree-element:last-child::after {
        border: 0 none;
    }

    :is([dir="ltr"]) .tree-element:last-child::before {
        border-right: 2px solid #ccc;
    }

    :is([dir="rtl"]) .tree-element:first-child::after,
    :is([dir="rtl"]) .tree-element:last-child::before {
        border: 0 none;
    }

    :is([dir="rtl"]) .tree-element:first-child::after {
        border-left: 2px solid #ccc;
    }
}
</style>
