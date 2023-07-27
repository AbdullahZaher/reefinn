<script setup>
const props = defineProps({
    large: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="loadingio-spinner-rolling-h6bnc4sd0gc" v-if="large">
        <div class="ldio-7q64vgqtyv">
            <div></div>
        </div>
    </div>

    <div class="loadingio-spinner-rolling-nmbubqofrbg" v-else>
        <div class="ldio-qq5bop5v85">
            <div></div>
        </div>
    </div>
</template>

<style type="text/css" scoped>
@keyframes ldio-7q64vgqtyv {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}
.ldio-7q64vgqtyv div {
    position: absolute;
    width: 60px;
    height: 60px;
    border: 10px solid #1d0e0b;
    border-top-color: transparent;
    border-radius: 50%;
}
.ldio-7q64vgqtyv div {
    animation: ldio-7q64vgqtyv 1s linear infinite;
    top: 50px;
    left: 50px;
}
.loadingio-spinner-rolling-h6bnc4sd0gc {
    width: 100px;
    height: 100px;
    display: inline-block;
    overflow: hidden;
    background: none;
}
.ldio-7q64vgqtyv {
    width: 100%;
    height: 100%;
    position: relative;
    transform: translateZ(0) scale(1);
    backface-visibility: hidden;
    transform-origin: 0 0;
}
.ldio-7q64vgqtyv div {
    box-sizing: content-box;
}

@keyframes ldio-qq5bop5v85 {
    0% {
        transform: translate(-50%, -50%) rotate(0deg);
    }
    100% {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}
.ldio-qq5bop5v85 div {
    position: absolute;
    width: 40px;
    height: 40px;
    border: 8px solid #1d0e0b;
    border-top-color: transparent;
    border-radius: 50%;
}
.ldio-qq5bop5v85 div {
    animation: ldio-qq5bop5v85 1s linear infinite;
    top: 30px;
    left: 30px;
}
.loadingio-spinner-rolling-nmbubqofrbg {
    width: 15px;
    height: 15px;
    display: inline-block;
    overflow: hidden;
    background: none;
}
.ldio-qq5bop5v85 {
    width: 100%;
    height: 100%;
    position: relative;
    transform: translateZ(0) scale(0.25);
    backface-visibility: hidden;
    transform-origin: 0 0;
}
.ldio-qq5bop5v85 div {
    box-sizing: content-box;
}
</style>
