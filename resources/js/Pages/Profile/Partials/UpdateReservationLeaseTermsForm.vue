<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Loader from "@/Components/Loader.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { __ } from "@/Composables/translations";
import { onMounted } from "vue";

const props = defineProps({
    terms: {
        type: Object,
    },
});

const form = useForm({
    terms: props.terms,
});

onMounted(() => {
    document.querySelectorAll("textarea").forEach((element) => {
        if (element.scrollHeight > element.offsetHeight) {
            element.style.height = element.scrollHeight + 20 + "px";
        }
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

        <form
            @submit.prevent="
                form.patch(route('reservations.terms.update'), {
                    preserveScroll: true,
                })
            "
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel
                    for="terms_en"
                    :value="__('Terms In English')"
                    required
                />

                <textarea
                    id="terms_en"
                    class="mt-1 block w-full"
                    v-model="form.terms['en']"
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
                    v-model="form.terms['ar']"
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
