<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import Apartment from "@/Components/Apartment.vue";
import ApartmentListItem from "@/Components/ApartmentListItem.vue";
import AddApartmentModal from "@/Components/Modals/AddApartmentModal.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    apartments: {
        type: Object,
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
    idTypes: {
        type: Object,
    },
    paymentMethods: {
        type: Object,
    },
});

const isCreateApartmentModalOpen = ref(false);

const viewMode = ref(usePage().props.auth.user.view_mode);
</script>

<template>
    <Head :title="__('Dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Dashboard") }}
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
                        <button
                            type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 w-full lg:w-32"
                            @click="isCreateApartmentModalOpen = true"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'create apartments'
                                )
                            "
                        >
                            {{ __("Add") }}
                        </button>

                        <AddApartmentModal
                            :apartmentTypes="apartmentTypes"
                            :apartmentDescriptions="apartmentDescriptions"
                            :open="isCreateApartmentModalOpen"
                            @close="isCreateApartmentModalOpen = false"
                            v-if="
                                isCreateApartmentModalOpen &&
                                $page.props.auth.user.can.includes(
                                    'create apartments'
                                )
                            "
                        />
                    </div>

                    <div class="flex justify-end items-center my-4">
                        <span class="p-1 inline-flex rounded-md">
                            <button
                                class="px-2 py-1 rounded"
                                :class="
                                    viewMode == 'grid' ? 'bg-white  shadow' : ''
                                "
                                @click="viewMode = 'grid'"
                            >
                                <FontAwesomeIcon
                                    icon="fas fa-table-cells-large"
                                    class="text-xl"
                                />
                            </button>
                            <button
                                class="px-2 py-1 rounded"
                                :class="
                                    viewMode == 'list' ? 'bg-white  shadow' : ''
                                "
                                @click="viewMode = 'list'"
                            >
                                <FontAwesomeIcon
                                    icon="fas fa-list"
                                    class="text-xl"
                                />
                            </button>
                        </span>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"
                        v-if="viewMode == 'grid'"
                    >
                        <Apartment
                            v-for="apartment in apartments.data"
                            :key="apartment.id"
                            :apartment="apartment"
                            :reservationTypes="reservationTypes"
                            :idTypes="idTypes"
                            :paymentMethods="paymentMethods"
                        />
                    </div>

                    <div
                        class="md:flex overflow-x-auto"
                        v-else-if="viewMode == 'list'"
                    >
                        <table
                            class="w-full whitespace-nowrap border-spacing-y-3 border-separate"
                        >
                            <tbody class="px-4">
                                <tr
                                    tabindex="0"
                                    class="focus:outline-none text-xs leading-none text-gray-600 h-6"
                                >
                                    <td
                                        class="w-1/4 ltr:pl-4 rtl:pr-4 text-xs font-medium leading-none text-gray-800"
                                    >
                                        <p>{{ __("Name") }}</p>
                                    </td>
                                    <td
                                        class="text-center text-xs font-medium leading-none text-gray-800"
                                    >
                                        <p>
                                            {{ __("Default Price For Night") }}
                                        </p>
                                    </td>
                                    <td>
                                        <p
                                            class="text-center text-xs font-medium leading-none text-gray-800"
                                        >
                                            {{ __("Check-in Date") }}
                                        </p>
                                    </td>
                                    <td>
                                        <p
                                            class="text-center text-xs font-medium leading-none text-gray-800"
                                        >
                                            {{ __("Check-out Date") }}
                                        </p>
                                    </td>
                                    <td>
                                        <p
                                            class="text-center text-xs font-medium leading-none text-gray-800"
                                        >
                                            {{ __("Amounts Due") }}
                                        </p>
                                    </td>
                                    <td>
                                        <p
                                            class="text-center text-xs font-medium leading-none text-gray-800"
                                        >
                                            {{ __("Actions") }}
                                        </p>
                                    </td>
                                </tr>

                                <ApartmentListItem
                                    v-for="apartment in apartments.data"
                                    :key="apartment.id"
                                    :apartment="apartment"
                                    :reservationTypes="reservationTypes"
                                    :idTypes="idTypes"
                                    :paymentMethods="paymentMethods"
                                />
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
