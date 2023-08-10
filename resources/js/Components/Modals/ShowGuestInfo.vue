<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    reservation: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);
</script>

<template>
    <Modal
        :headerTitle="`${__('Guest for reservation')} '${reservation.id}'`"
        :open="open"
        @close="$emit('close')"
    >
        <div class="text-center text-xl">
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("Guest Name") }}:</span>
                {{ reservation.guest_name }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("ID Type") }}:</span>
                {{ __(reservation.id_type) }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("Guest ID") }}:</span>
                {{ reservation.guest_id }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("Copy") }}:</span>
                {{ reservation.copy }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("Guest Phone") }}:</span>
                {{ reservation.guest_phone }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("Guest Birthday") }}:</span>
                {{ reservation.guest_birthday }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("Number Of Companions") }}:</span>
                {{ reservation.number_of_companions }}
            </div>
            <div
                class="py-5 border px-6 flex items-center gap-2 justify-center"
                v-if="reservation.rating"
            >
                <span class="font-bold">{{ __("Guest Rating") }}:</span>

                <div class="flex items-center">
                    <template
                        v-for="(i, j) in Array.from({ length: 5 })"
                        :key="i"
                    >
                        <FontAwesomeIcon
                            icon="fas fa-star"
                            class="text-yellow-400"
                            v-if="reservation.rating >= j + 1"
                        />
                        <FontAwesomeIcon
                            icon="far fa-star"
                            class="text-yellow-400"
                            v-else
                        />
                    </template>
                </div>
            </div>
        </div>
    </Modal>
</template>
