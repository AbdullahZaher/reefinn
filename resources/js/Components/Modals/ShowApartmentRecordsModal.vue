<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    apartment: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);
</script>

<template>
    <Modal
        :headerTitle="`${__('Records for')} '${apartment.name}'`"
        :open="open"
        @close="$emit('close')"
    >
        <div
            class="relative overflow-x-auto shadow-md sm:rounded-lg max-h-[70vh]"
            v-if="apartment.records.length"
        >
            <table class="w-full text-sm text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">#</th>
                        <th scope="col" class="px-6 py-3">{{ __("State") }}</th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Reservation") }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Employee Name") }}
                        </th>
                        <th scope="col" class="px-6 py-3">{{ __("Note") }}</th>
                        <th scope="col" class="px-6 py-3">
                            {{ __("Created At") }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b hover:bg-gray-50"
                        v-for="(record, index) in apartment.records"
                        :key="index"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">
                            {{ record.state_from }}
                            <span>{{
                                $page.props.locale.dir == "ltr"
                                    ? "&rarr;"
                                    : "&larr;"
                            }}</span>
                            {{ record.state_to }}
                        </td>
                        <td class="px-6 py-4">
                            <div v-if="!record.reservation">-</div>
                            <div v-else>
                                <p class="whitespace-nowrap">
                                    {{ __("Name") }}:
                                    {{ record.reservation.guest_name }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Guest ID") }}:
                                    {{ record.reservation.guest_id }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Copy") }}:
                                    {{ record.reservation.copy }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Guest Birthday") }}:
                                    {{ record.reservation.guest_birthday }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Phone") }}:
                                    {{ record.reservation.guest_phone }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Number Of Companions") }}:
                                    {{
                                        record.reservation.number_of_companions
                                    }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Check-in") }}:
                                    {{ record.reservation.checkin }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Check-out") }}:
                                    {{ record.reservation.checkout }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Price For Night") }}:
                                    {{
                                        record.reservation.price_for_night +
                                        " " +
                                        __("SAR")
                                    }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Total Price") }}:
                                    {{
                                        record.reservation.total_price +
                                        " " +
                                        __("SAR")
                                    }}
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Discount") }}:
                                    <template
                                        v-if="record.reservation.discount > 0"
                                    >
                                        <span class="font-medium">
                                            {{
                                                record.reservation.discount +
                                                "%"
                                            }}
                                        </span>
                                        <span class="text-sm">
                                            ({{
                                                record.reservation
                                                    .discount_amount +
                                                " " +
                                                __("SAR")
                                            }})
                                        </span>
                                    </template>
                                    <span v-else>-</span>
                                </p>
                                <p class="whitespace-nowrap">
                                    {{ __("Amounts Due") }}:
                                    {{
                                        record.reservation.amounts_due +
                                        " " +
                                        __("SAR")
                                    }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ record.admin_name ?? "-" }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ record.note }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ record.created_at }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center font-bold text-lg" v-else>
            {{ __("Apartment doesn't have any records.") }}
        </div>
    </Modal>
</template>
