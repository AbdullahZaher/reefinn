<script setup>
import { __ } from "@/Composables/translations";

const props = defineProps({
    apartments: {
        type: Array,
    },
});
</script>

<template>
    <div class="text-center text-4xl font-bold">
        {{ __("Top Reserved Apartments") }}
    </div>
    <div class="mt-12 overflow-x-auto max-h-[60vh]">
        <table class="text-sm text-center text-gray-500 w-full">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">#</th>
                    <th scope="col" class="px-6 py-3">
                        {{ __("Name") }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __("Pending Reservations") }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __("Checkin Reservations") }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __("Checkout Reservations") }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __("Canceled Reservations") }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __("Total Reservations") }}
                    </th>
                    <th scope="col" class="px-6 py-3">
                        {{ __("Total Profit") }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="bg-white border-b"
                    v-for="(apartment, index) in apartments"
                    :key="apartment.id"
                >
                    <th
                        scope="row"
                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
                    >
                        {{ index + 1 }}
                    </th>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ apartment.name }}
                    </td>
                    <td class="px-6 py-4 max-w-xs whitespace-nowrap font-bold">
                        {{ apartment.pending_reservations_count }}
                    </td>
                    <td class="px-6 py-4 max-w-xs whitespace-nowrap font-bold">
                        {{ apartment.checkin_reservations_count }}
                    </td>
                    <td class="px-6 py-4 max-w-xs whitespace-nowrap font-bold">
                        {{ apartment.checkout_reservations_count }}
                    </td>
                    <td class="px-6 py-4 max-w-xs whitespace-nowrap font-bold">
                        {{ apartment.canceled_reservations_count }}
                    </td>
                    <td class="px-6 py-4 max-w-xs whitespace-nowrap font-bold">
                        {{ apartment.reservations_count }}
                    </td>
                    <td class="px-6 py-4 max-w-xs whitespace-nowrap">
                        <span class="font-bold">
                            {{ apartment.total_profit }}
                        </span>
                        {{ __("SAR") }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
