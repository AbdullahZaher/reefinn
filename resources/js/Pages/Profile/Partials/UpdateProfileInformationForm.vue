<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Loader from "@/Components/Loader.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { __ } from "@/Composables/translations";

defineProps({
    timezones: {
        type: Array,
    },
    calendars: {
        type: Array,
    },
    viewModes: {
        type: Array,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    timezone: user.timezone,
    calendar: user.calendar,
    view_mode: user.view_mode,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __("Profile Information") }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{
                    __(
                        "Update your account's profile information and email address."
                    )
                }}
            </p>
        </header>

        <form
            @submit.prevent="
                form.patch(route('profile.update'), { preserveScroll: true })
            "
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" :value="__('Name')" required />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" :value="__('Email')" required />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="timezones" :value="__('Timezone')" required />

                <select
                    id="timezones"
                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1"
                    style="direction: ltr"
                    v-model="form.timezone"
                >
                    <option disabled>{{ __("Choose a option") }}</option>
                    <option
                        :value="timezone"
                        v-for="(timezone, index) in timezones"
                        :key="index"
                    >
                        {{ timezone }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.timezone" />
            </div>

            <div>
                <InputLabel for="calendar" :value="__('Calendar')" required />

                <select
                    id="calendar"
                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1 rtl"
                    v-model="form.calendar"
                >
                    <option disabled>{{ __("Choose a option") }}</option>
                    <option
                        :value="calendar.value"
                        v-for="(calendar, index) in calendars"
                        :key="index"
                    >
                        {{ calendar.label }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.calendar" />
            </div>

            <div>
                <InputLabel for="view_mode" :value="__('View Mode')" required />

                <select
                    id="viewMode"
                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 mt-1 rtl"
                    v-model="form.view_mode"
                >
                    <option disabled>{{ __("Choose a option") }}</option>
                    <option
                        :value="viewMode.value"
                        v-for="(viewMode, index) in viewModes"
                        :key="index"
                    >
                        {{ viewMode.label }}
                    </option>
                </select>

                <InputError class="mt-2" :message="form.errors.viewMode" />
            </div>

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
