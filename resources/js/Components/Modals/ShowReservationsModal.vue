<script setup>
import { ref, watch } from "vue";
import Modal from "@/Components/Modals/Modal.vue";
import AddReservationModal from "@/Components/Modals/AddReservationModal.vue";
import Reservation from "@/Components/Reservation.vue";
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

const isAddReservationModalOpen = ref(false);

const clickOutsideToClose = ref(true);

watch(
    isAddReservationModalOpen,
    (value) => (clickOutsideToClose.value = !value)
);
</script>

<template>
    <Modal
        :headerTitle="`${__('Reservations for')} '${apartment.name}'`"
        :open="open"
        :clickOutsideToClose="clickOutsideToClose"
        @close="$emit('close')"
    >
        <AddReservationModal
            :apartment="apartment"
            :open="isAddReservationModalOpen"
            @close="isAddReservationModalOpen = false"
            @closeParent="$emit('close')"
            v-if="isAddReservationModalOpen"
        />

        <div class="mb-5">
            <button
                type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 w-full"
                @click="isAddReservationModalOpen = true"
                v-if="$page.props.auth.user.can.includes('create reservations')"
            >
                {{ __("Add") }}
            </button>
        </div>

        <div
            class="relative grid grid-cols-1 md:grid-cols-2 gap-6"
            v-if="apartment.reservations.length"
        >
            <div
                class="w-full rounded-lg p-12 flex flex-col justify-center items-center relative"
                :class="`bg-orange-400`"
                v-for="reservation in apartment.reservations"
                :key="reservation.id"
            >
                <Reservation
                    :apartment="apartment"
                    :reservation="reservation"
                    @close="$emit('close')"
                    v-model:clickOutsideToClose="clickOutsideToClose"
                />
            </div>
        </div>

        <div class="text-center font-bold text-lg" v-else>
            {{ __("Apartment doesn't have any reservations.") }}
        </div>
    </Modal>
</template>
