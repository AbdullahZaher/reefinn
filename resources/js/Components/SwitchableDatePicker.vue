<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import VueDatetimeJs from "vue3-datetime-js";
import moment from "moment-hijri";
import { __ } from "@/Composables/translations";

const props = defineProps({
    modelValue: {
        type: String,
    },
});

const emit = defineEmits(["update:modelValue"]);

const _value = ref(props.modelValue);

const calendars = [
    { key: "gregory", value: __("Gregorian") },
    { key: "hijri", value: __("Hijri") },
];
const _calendar = ref("gregory");

const localeConfig = {
    gregory: {
        dow: 0,
        dir: usePage().props.locale.dir,
        lang: {
            submit: __("Confirm"),
            cancel: __("Cancel"),
            now: __("Now"),
            nextMonth: __("Next month"),
            prevMonth: __("Previous month"),
        },
    },
    hijri: {
        dow: 0,
        dir: usePage().props.locale.dir,
        lang: {
            submit: __("Confirm"),
            cancel: __("Cancel"),
            now: __("Now"),
            nextMonth: __("Next month"),
            prevMonth: __("Previous month"),
        },
    },
};
</script>

<template>
    <div class="flex gap-5 flex-col">
        <VueDatetimeJs
            class="md:flex-1"
            :value="_value"
            @input="
                ($event) => {
                    $emit('update:modelValue', $event);
                    _value = $event;
                }
            "
            calendar="gregory"
            :placeholder="__('Please select a date')"
            inputClass="w-full !py-1"
            format="YYYY-MM-DD"
            displayFormat="DD MMMM YYYY"
            :locale="$page.props.locale.lang == 'ar' ? 'ar-sa' : 'en'"
            :localeConfig="localeConfig"
            v-if="_calendar == 'gregory'"
        />

        <VueDatetimeJs
            class="md:flex-1"
            :value="_value"
            @input="
                ($event) => {
                    const d = moment($event, 'iYYYY-iMM-iDD').format(
                        'YYYY-MM-DD'
                    );
                    $emit('update:modelValue', d);
                    _value = d;
                }
            "
            calendar="hijri"
            :placeholder="__('Please select a date')"
            inputClass="w-full !py-1"
            inputFormat="YYYY-MM-DD"
            displayFormat="iDD iMMMM iYYYY"
            :locale="$page.props.locale.lang == 'ar' ? 'ar-sa' : 'en'"
            :localeConfig="localeConfig"
            v-else-if="_calendar == 'hijri'"
        />

        <div
            class="flex items-center justify-center space-x-4 rtl:space-x-reverse"
        >
            <span class="text-base font-medium">
                {{ calendars[0].value }}
            </span>
            <button
                type="button"
                class="relative rounded-full focus:outline-none"
                @click="
                    _calendar == calendars[0].key
                        ? (_calendar = calendars[1].key)
                        : (_calendar = calendars[0].key)
                "
            >
                <div
                    class="w-12 h-6 transition bg-blue-500 rounded-full shadow-md outline-none"
                ></div>

                <div
                    class="absolute inline-flex items-center justify-center w-4 h-4 transition-all duration-200 ease-in-out transform bg-white rounded-full shadow-sm top-1 left-1"
                    :class="{
                        'translate-x-0 rtl:translate-x-6':
                            _calendar == calendars[0].key,
                        'translate-x-6 rtl:translate-x-0':
                            _calendar == calendars[1].key,
                    }"
                ></div>
            </button>
            <span class="text-base font-medium">
                {{ calendars[1].value }}
            </span>
        </div>
    </div>
</template>

<style>
.vpd-input-group input {
    border-right: 1px solid #dadada !important;
}
.vpd-month-label {
    overflow: visible !important;
}
</style>
