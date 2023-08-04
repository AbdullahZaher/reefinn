<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ExpensesModal from "@/Components/Modals/ExpensesModal.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    expenses: {
        type: Object,
    },
});

const isExpensesModalOpen = ref(false);
</script>

<template>
    <Head :title="__('Finance and Tax')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Finance and Tax") }}
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
                            {{ __("Finance and Tax") }}
                        </h1>
                    </div>

                    <ExpensesModal
                        :open="isExpensesModalOpen"
                        @close="isExpensesModalOpen = false"
                        :expenses="expenses"
                    />

                    <div
                        class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            class="border-2 border-gray-100 h-64 w-full bg-white rounded-3xl text-4xl font-bold shadow-2xl cursor-pointer select-none hover:scale-110 transition-all ease-in-out duration-200 flex items-center justify-center"
                            @click="isExpensesModalOpen = true"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'show expenses'
                                )
                            "
                        >
                            <span>{{ __("Expenses") }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>