<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import Apartment from "@/Components/Apartment.vue";
import AddApartmentModal from "@/Components/Modals/AddApartmentModal.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    apartments: {
        type: Object,
    },
});

const isCreateApartmentModalOpen = ref(false);
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-4 py-12">
                    <div
                        class="text-center pb-12 flex items-center justify-between flex-col lg:flex-row space-y-6 lg:space-y-0"
                    >
                        <h1
                            class="font-bold text-5xl font-heading text-gray-900"
                        >
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
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <Apartment
                            v-for="apartment in apartments.data"
                            :key="apartment.id"
                            :apartment="apartment"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
