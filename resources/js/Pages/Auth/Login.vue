<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Loader from "@/Components/Loader.vue";
import { __ } from "@/Composables/translations";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="__('Log in')" />

        <div
            class="mb-4 font-bold text-2xl text-center flex items-center justify-center space-x-1"
        >
            <div>{{ __("Log in") }}</div>
            <LanguageSwitcher />
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="__('Email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="__('Password')" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span
                        class="text-sm text-gray-600"
                        :class="[
                            $page.props.locale.dir == 'ltr' ? 'ml-2' : 'mr-2',
                        ]"
                    >
                        {{ __("Remember me") }}
                    </span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    {{ __("Forgot your password?") }}
                </Link>

                <PrimaryButton
                    :class="{
                        'bg-opacity-25 hover:bg-gray-800 hover:bg-opacity-25 space-x-2 rtl:space-x-reverse':
                            form.processing,
                    }"
                    :disabled="form.processing"
                >
                    <Loader v-if="form.processing" />
                    <span>{{ __("Log in") }}</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
