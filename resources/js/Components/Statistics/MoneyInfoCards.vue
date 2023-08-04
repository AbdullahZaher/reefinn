<script setup>
import { __ } from "@/Composables/translations";

const props = defineProps({
    counts: {
        type: Object,
    },
});
</script>

<template>
    <div
        class="flex flex-col lg:flex-row space-y-8 lg:space-y-0 mb-2 lg:mb-4 relative"
    >
        <div class="w-full lg:w-1/2 px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4 border-emerald-400"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center bg-emerald-400"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-money-bills"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.income + " " + __("SAR") }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Income") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/2 px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4 border-rose-400"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center bg-rose-400"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-money-bill-transfer"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.expenses + " " + __("SAR") }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Expenses") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
