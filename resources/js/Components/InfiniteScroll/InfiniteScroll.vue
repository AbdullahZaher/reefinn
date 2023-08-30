<script setup>
import { onMounted, onBeforeUnmount, ref, getCurrentInstance } from "vue";
import { debounce } from "lodash";
import SpinLoader from "@/Components/SpinLoader.vue";
import { __ } from "@/Composables/translations";

const props = defineProps({
    loadMore: {
        type: Function,
    },
    loadMoreText: {
        type: String,
        default: __("Loading"),
    },
});

const loading = ref(false);

const contentElement = ref(
    getCurrentInstance().parent.parent.exposed?.infinteScrollContentElement
);

let latestScrollTop = 0;
const handleScroll = debounce((e) => {
    if (contentElement.value.scrollTop != latestScrollTop) {
        latestScrollTop = contentElement.value.scrollTop;
        let pixelsFromBottom =
            contentElement.value.scrollHeight - contentElement.value.scrollTop;

        if (pixelsFromBottom < 1500 && !loading.value) {
            loading.value = true;
            props.loadMore().finally(() => (loading.value = false));
        }
    }
}, 200);

onMounted(() => {
    if (!contentElement.value) contentElement.value = document.documentElement;

    contentElement?.value
        ? contentElement.value.addEventListener("scroll", handleScroll)
        : window.addEventListener("scroll", handleScroll);
});

onBeforeUnmount(() =>
    contentElement?.value
        ? contentElement.value.removeEventListener("scroll", handleScroll)
        : window.removeEventListener("scroll", handleScroll)
);
</script>

<template>
    <slot />
    <div
        class="flex items-center justify-center my-5 space-x-2 rtl:space-x-reverse"
        v-if="loading"
    >
        <SpinLoader size="30px" stroke="2px" background="#000" color="#fff" />
        <span>{{ props.loadMoreText }}</span>
    </div>
</template>
