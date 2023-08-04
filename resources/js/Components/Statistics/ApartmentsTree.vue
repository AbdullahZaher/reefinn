<script setup>
import { __ } from "@/Composables/translations";

const props = defineProps({
    counts: {
        type: Object,
    },
    apartmentStateColors: {
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
                class="widget w-full p-4 rounded-lg bg-white border-l-4 border-purple-400"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 bg-purple-400 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-building"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.apartments }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Apartments") }}
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
                :class="`border-${apartmentStateColors.empty}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${apartmentStateColors.empty}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-building"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.empty_apartments }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("EmptyApartments") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/4 tree-element px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4"
                :class="`border-${apartmentStateColors.reserved}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${apartmentStateColors.reserved}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-building"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.reserved_apartments }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Reserved Apartments") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/4 tree-element px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4"
                :class="`border-${apartmentStateColors.inhabited}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${apartmentStateColors.inhabited}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-building"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.inhabited_apartments }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Inhabited Apartments") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/4 tree-element px-3">
            <div
                class="widget w-full p-4 rounded-lg bg-white border-l-4"
                :class="`border-${apartmentStateColors.cleaning}-400`"
            >
                <div class="flex items-center">
                    <div
                        class="w-14 h-14 text-white rounded-full mr-3 rtl:mr-0 rtl:ml-3 flex items-center justify-center"
                        :class="`bg-${apartmentStateColors.cleaning}-400`"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-building"
                            class="text-2xl"
                        />
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="text-lg font-bold">
                            {{ counts.cleaning_apartments }}
                        </div>
                        <div class="text-sm text-gray-400">
                            {{ __("Cleaning Apartments") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
