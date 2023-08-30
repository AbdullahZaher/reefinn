<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { ref, onMounted } from "vue";
import AddReservationModal from "@/Components/Modals/AddReservationModal.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    apartment: {
        type: Object,
        default: null,
    },
    reservation: {
        type: Object,
    },
    idTypes: {
        type: Object,
    },
    paymentMethods: {
        type: Object,
    },
});

const emit = defineEmits(["close"]);

const apartments = ref([]);

const apartmentIndex = ref(0);
const apartment = ref(null);

const isAddReservationModalOpen = ref(false);

const _submitHandler = () => {
    apartment.value = apartments.value[apartmentIndex.value];
    isAddReservationModalOpen.value = true;
};

const loading = ref(true);

const getAvailableApartments = async () => {
    await axios
        .post(route("apartments.available"), {
            apartment: props.apartment?.id,
            checkin: props.reservation.checkin,
            checkout: props.reservation.checkout,
        })
        .then((response) => {
            if (props.apartment) {
                if (!response.data?.status) {
                    return Swal.fire({
                        icon: "error",
                        title: __("Sorry..."),
                        text: __(
                            "There a reservation for this apartment in this period!"
                        ),
                        confirmButtonText: __("Ok"),
                    }).finally(() => emit("close"));
                }

                apartment.value = props.apartment;
                isAddReservationModalOpen.value = true;
            } else {
                if (response.data?.data.length == 0) {
                    return Swal.fire({
                        icon: "error",
                        title: __("Sorry..."),
                        text: __(
                            "There is no available apartments for this period!"
                        ),
                        confirmButtonText: __("Ok"),
                    }).finally(() => emit("close"));
                }

                apartments.value = response.data.data;
            }
        })
        .catch(async (e) => {
            await Swal.fire({
                icon: "error",
                title: __("Sorry..."),
                text: __("Something went wrong!"),
                confirmButtonText: __("Ok"),
            }).finally(() => emit("close"));
        })
        .finally(() => (loading.value = false));
};

onMounted(async () => {
    await getAvailableApartments();
});
</script>

<template>
    <Modal
        :headerTitle="`${__(
            'Available apartments for this period'
        )}: <span class='whitespace-nowrap'>(${reservation.checkin}) - (${
            reservation.checkout
        })</span>`"
        :open="open"
        @close="$emit('close')"
        :clickOutsideToClose="!isAddReservationModalOpen"
    >
        <AddReservationModal
            :apartment="apartment"
            :checkin="reservation.checkin"
            :checkout="reservation.checkout"
            :idTypes="idTypes"
            :paymentMethods="paymentMethods"
            :open="isAddReservationModalOpen"
            @close="
                isAddReservationModalOpen = false;
                if (props.apartment) $emit('close');
            "
            @closeParent="$emit('close')"
            forceParentClose
            v-if="
                isAddReservationModalOpen &&
                $page.props.auth.user.can.includes('create reservations')
            "
        />

        <div class="flex items-center justify-center my-10" v-if="loading">
            <div class="loadingio-spinner-rolling-dj99k3zvmov">
                <div class="ldio-09hfvpy8y6h7">
                    <div></div>
                </div>
            </div>
        </div>

        <form class="space-y-6" v-else>
            <div>
                <label
                    for="apartments"
                    class="block mb-2 text-sm font-medium text-gray-900"
                >
                    {{ __("Select an apartment") }}
                    <span class="text-red-600 text-sm">*</span>
                </label>

                <select
                    id="apartments"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 rtl"
                    v-model="apartmentIndex"
                >
                    <option
                        v-for="(apartment, index) in apartments"
                        :key="apartment.id"
                        :value="index"
                    >
                        {{ apartment.name }} ({{ __(apartment.type) }})
                    </option>
                </select>
            </div>

            <div class="flex items-center justify-end ltr:ml-auto rtl:mr-auto">
                <button
                    type="submit"
                    class="text-white bg-blue-700 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 flex items-center justify-center space-x-2 rtl:space-x-reverse hover:bg-blue-800"
                    @click.prevent="_submitHandler"
                >
                    <span>{{ __("Save") }}</span>
                </button>
            </div>
        </form>
    </Modal>
</template>

<style type="text/css" scoped>
@keyframes ldio-09hfvpy8y6h7 {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}
.ldio-09hfvpy8y6h7 div {
    position: absolute;
    width: 120px;
    height: 120px;
    border: 20px solid #1d0e0b;
    border-top-color: transparent;
    border-radius: 50%;
}
.ldio-09hfvpy8y6h7 div {
    animation: ldio-09hfvpy8y6h7 1s linear infinite;
    top: 100px;
    left: 100px;
}
.loadingio-spinner-rolling-dj99k3zvmov {
    width: 200px;
    height: 200px;
    display: inline-block;
    overflow: hidden;
    background: none;
}
.ldio-09hfvpy8y6h7 {
    width: 100%;
    height: 100%;
    position: relative;
    transform: translateZ(0) scale(1);
    backface-visibility: hidden;
    transform-origin: 0 0;
}
.ldio-09hfvpy8y6h7 div {
    box-sizing: content-box;
}
</style>
