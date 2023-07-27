<script setup>
import Modal from "@/Components/Modals/Modal.vue";
import { ref, onMounted, nextTick } from "vue";
import { usePage } from "@inertiajs/vue3";
import { usePaperizer } from "paperizer";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    open: {
        type: Boolean,
    },
    routeUrl: {
        type: String,
    },
    windowTitle: {
        type: [String, Number],
    },
});

const emit = defineEmits(["close"]);

const elmId = `print-${Date.now()}`;
const html = ref(null);

const getHtml = async () => {
    await axios
        .get(props.routeUrl)
        .then((response) => {
            html.value = response.data;
        })
        .catch(async () => {
            await Swal.fire({
                icon: "error",
                title: __("Sorry..."),
                text: __("Something went wrong!"),
                confirmButtonText: __("Ok"),
            }).finally(() => emit("close"));
        });
};

const print = () => {};

onMounted(async () => {
    await getHtml();

    await nextTick();

    const { paperize } = usePaperizer(
        elmId,
        {
            windowTitle: props.windowTitle,
            styles: [usePage().props.styleUrl],
        },
        () => emit("close")
    );
    paperize();
});
</script>

<template>
    <Modal
        :headerTitle="windowTitle"
        :closable="false"
        :open="open"
        @close="$emit('close')"
    >
        <div class="flex items-center justify-center my-10">
            <div class="loadingio-spinner-rolling-dj99k3zvmov">
                <div class="ldio-09hfvpy8y6h7">
                    <div></div>
                </div>
            </div>
        </div>

        <div :id="elmId" class="hidden print:block" v-html="html"></div>
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
