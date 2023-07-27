<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { ref, onMounted, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Loader from "@/Components/Loader.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    reservation: {
        type: Object,
    },
    apartmentId: {
        type: [String, Number],
    },
});

const emit = defineEmits(["close"]);

const _apartments = ref([]);
const apartments = computed(() =>
    _apartments.value.filter((apartment) => apartment.id != props.apartmentId)
);

const _form = useForm({
    apartment_id: "",
});

const _submitHandler = () => {
    _form.clearErrors();

    _form.patch(route("reservations.transfer", props.reservation.id), {
        preserveState: false,
        preserveScroll: true,
        onSuccess: () => {
            _form.reset();
            emit("close");
        },
    });
};

const loading = ref(true);

const getAvailableApartments = async () => {
    await axios
        .post(route("apartments.available"), {
            checkin: props.reservation.checkin,
            checkout: props.reservation.checkout,
        })
        .then((response) => {
            _apartments.value = response.data.data;
            _form.apartment_id = apartments.value[0].id;
        })
        .catch(async (e) => {
            console.log(e);
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
        :headerTitle="`${__('Transfer')} '${props.reservation.guest_name}' ${__(
            'to another apartment'
        )}`"
        :open="open"
        @close="$emit('close')"
    >
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
                </label>
                <select
                    id="apartments"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    dir="ltr"
                    v-model="_form.apartment_id"
                >
                    <option
                        v-for="apartment in apartments"
                        :key="apartment.id"
                        :value="apartment.id"
                    >
                        {{ apartment.name }}
                    </option>
                </select>

                <span class="text-red-600 text-sm mt-1">
                    {{ _form.errors.apartment_id }}
                </span>
            </div>

            <div
                class="flex items-center justify-end"
                :class="[
                    $page.props.locale.dir == 'ltr' ? 'ml-auto' : 'mr-auto',
                ]"
            >
                <button
                    type="submit"
                    class="text-white bg-blue-700 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 flex items-center justify-center space-x-2 rtl:space-x-reverse"
                    :class="[
                        _form.processing
                            ? 'bg-opacity-50'
                            : 'hover:bg-blue-800',
                    ]"
                    @click.prevent="_submitHandler"
                    :disabled="_form.processing"
                >
                    <Loader v-if="_form.processing" />
                    <span>{{ __("Transfer") }}</span>
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
