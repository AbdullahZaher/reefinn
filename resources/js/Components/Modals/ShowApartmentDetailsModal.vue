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
        :headerTitle="`${__('Apartment Details')} '${apartment.name}'`"
        :open="open"
        @close="$emit('close')"
    >
        <div class="text-center text-xl">
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("ID") }}:</span>
                {{ apartment.id }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold">{{ __("Description") }}:</span>
                {{ apartment.description ?? "-" }}
            </div>
            <div class="py-5 border px-6">
                <span class="font-bold"
                    >{{ __("Default Price For Night") }}:</span
                >
                {{ apartment.price_for_night + " " + __("SAR") }}
            </div>
        </div>
    </Modal>
</template>
