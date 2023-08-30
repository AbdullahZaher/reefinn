<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import ApartmentItem from "@/Components/ApartmentItem.vue";
import InfiniteScroll from "@/Components/InfiniteScroll/InfiniteScroll.vue";
import useInfiniteScroll from "@/Composables/useInfiniteScroll.js";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import { throttle } from "lodash";
import { __ } from "@/Composables/translations";

const props = defineProps({
    filters: {
        type: Object,
    },
    apartments: {
        type: Object,
    },
    states: {
        type: Array,
    },
    apartmentTypes: {
        type: Object,
    },
    apartmentDescriptions: {
        type: Object,
    },
    reservationTypes: {
        type: Object,
    },
});

const apartments = ref(props.apartments);

watch(
    () => props.apartments,
    (value) => (apartments.value = value)
);

const infinteScrollContentElement = ref(null);
const loadMore = () =>
    useInfiniteScroll(apartments).loadMore({
        states: filters.value.states.join(","),
    });

defineExpose({
    infinteScrollContentElement,
});

// Filtering
const filters = ref({
    states: props.filters.states,
});

watch(
    filters,
    throttle((filters) => {
        router.get(
            route("apartments.index"),
            {
                states: filters.states.join(","),
            },
            {
                preserveState: true,
                only: ["apartments", "filters"],
            }
        );
    }, 250),
    { deep: true }
);
</script>

<template>
    <Head :title="__('Apartments')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Apartments") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-[100rem] mx-auto sm:px-6 lg:px-8">
                <div class="px-4 sm:px-6 lg:px-4 py-12">
                    <div
                        class="text-center pb-12 flex items-center justify-between flex-col lg:flex-row space-y-6 lg:space-y-0"
                    >
                        <h1 class="font-bold text-5xl text-gray-900">
                            {{ __("Apartments") }}
                        </h1>
                        <Dropdown
                            :title="__('States')"
                            icon="fa-solid fa-microchip"
                        >
                            <DropdownItem
                                v-for="(state, index) in states"
                                :key="index"
                                :title="__(state.value)"
                                :value="state.key"
                                v-model="filters.states"
                            />
                        </Dropdown>
                    </div>

                    <div
                        class="relative overflow-x-auto shadow-md sm:rounded-lg max-h-[70vh]"
                    >
                        <InfiniteScroll :loadMore="loadMore">
                            <table
                                class="w-full text-sm text-left rtl:text-right text-gray-500"
                            >
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50"
                                >
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("ID") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("State") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Name") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Type") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Description") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Price For Night") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Note") }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-center"
                                        >
                                            {{ __("Actions") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <ApartmentItem
                                        v-for="apartment in apartments.data"
                                        :key="apartment.id"
                                        :apartment="apartment"
                                        :apartmentTypes="apartmentTypes"
                                        :apartmentDescriptions="
                                            apartmentDescriptions
                                        "
                                        :reservationTypes="reservationTypes"
                                    />
                                </tbody>
                            </table>
                        </InfiniteScroll>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
