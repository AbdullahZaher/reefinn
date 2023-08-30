<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Loader from "@/Components/Loader.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { __ } from "@/Composables/translations";
import { onMounted, ref } from "vue";

const props = defineProps({
    types: {
        type: Object,
    },
    terms: {
        type: Object,
    },
});

const currentType = ref(1);

const form = useForm({
    terms: props.terms,
});

const submitHandler = () => {
    form.patch(route("reservations.terms.update"), {
        preserveScroll: true,
    });
};

onMounted(() => {
    document.querySelectorAll("textarea").forEach((element) => {
        if (element.scrollHeight > element.offsetHeight) {
            element.style.height = element.scrollHeight + 20 + "px";
        }

        element.addEventListener("input", (event) => {
            event.target.style.height = "auto";
            event.target.style.height = event.target.scrollHeight + 20 + "px";
        });
    });
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __("Reservation Lease Terms") }}
            </h2>
        </header>

        <div class="mx-auto my-8">
            <div class="flex w-full relative">
                <template v-for="(type, key) in types" :key="key">
                    <input
                        type="radio"
                        :id="`option-${key}`"
                        name="tabs"
                        class="appearance-none hidden"
                        v-model="currentType"
                        :value="key"
                    />
                    <label
                        :for="`option-${key}`"
                        class="cursor-pointer w-1/6 flex items-center justify-center truncate uppercase select-none font-semibold text-lg rounded-full py-2 z-10"
                        :class="{
                            'text-white': currentType == key,
                        }"
                    >
                        {{ __(type) }}
                    </label>
                </template>
                <div
                    class="w-1/6 flex items-center justify-center truncate uppercase select-none font-semibold text-lg rounded-full p-0 h-full bg-indigo-600 absolute transform transition-transform tabAnim"
                ></div>
            </div>
        </div>

        <form @submit.prevent="submitHandler" class="mt-6 space-y-6">
            <div>
                <InputLabel
                    for="terms_en"
                    :value="__('Terms In English')"
                    required
                />
                <textarea
                    id="terms_en"
                    class="mt-1 block w-full"
                    v-model="form.terms[currentType]['en']"
                    oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'
                    dir="ltr"
                ></textarea>
                <InputError class="mt-2" :message="form.errors['terms.en']" />
            </div>

            <div>
                <InputLabel
                    for="terms_ar"
                    :value="__('Terms In Arabic')"
                    required
                />
                <textarea
                    id="terms_ar"
                    class="mt-1 block w-full"
                    v-model="form.terms[currentType]['ar']"
                    oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'
                    dir="rtl"
                ></textarea>
                <InputError class="mt-2" :message="form.errors['terms.ar']" />
            </div>

            <InputError class="mt-2" :message="form.errors.terms" />

            <div class="flex items-center gap-4">
                <PrimaryButton
                    :disabled="form.processing"
                    :class="{
                        'bg-opacity-25 hover:bg-gray-800 hover:bg-opacity-25 space-x-2 rtl:space-x-reverse':
                            form.processing,
                    }"
                >
                    <Loader v-if="form.processing" />
                    <span>{{ __("Save") }}</span>
                </PrimaryButton>
            </div>
        </form>
    </section>
</template>

<style>
[dir="ltr"] #option-1:checked ~ div {
    --tw-translate-x: 0%;
}

[dir="ltr"] #option-2:checked ~ div {
    --tw-translate-x: 100%;
}

[dir="ltr"] #option-3:checked ~ div {
    --tw-translate-x: 200%;
}

[dir="rtl"] #option-1:checked ~ div {
    --tw-translate-x: -0%;
}

[dir="rtl"] #option-2:checked ~ div {
    --tw-translate-x: -100%;
}

[dir="rtl"] #option-3:checked ~ div {
    --tw-translate-x: -200%;
}
</style>
