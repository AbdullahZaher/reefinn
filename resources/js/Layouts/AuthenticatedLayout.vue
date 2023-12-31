<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import { Link } from "@inertiajs/vue3";
import ToastMessage from "@/Components/ToastMessage.vue";
import { __ } from "@/Composables/translations";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <ToastMessage :toast="$page.props.flash" />

    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-[100rem] mx-auto px-4 lg:px-6">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 rtl:space-x-reverse lg:-my-px lg:flex"
                                :class="[
                                    $page.props.locale.dir == 'ltr'
                                        ? 'lg:ml-10'
                                        : 'lg:mr-10',
                                ]"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                    v-if="
                                        $page.props.auth.user.can.includes(
                                            'show apartments'
                                        )
                                    "
                                >
                                    {{ __("Dashboard") }}
                                </NavLink>

                                <NavLink
                                    :href="route('reservations.index')"
                                    :active="
                                        route().current('reservations.index')
                                    "
                                    v-if="
                                        $page.props.auth.user.can.includes(
                                            'show reservations'
                                        )
                                    "
                                >
                                    {{ __("Reservations") }}
                                </NavLink>

                                <NavLink
                                    :href="route('calendar.index')"
                                    :active="route().current('calendar.index')"
                                    v-if="
                                        $page.props.auth.user.can.includes(
                                            'show calendar'
                                        )
                                    "
                                >
                                    {{ __("Calendar") }}
                                </NavLink>

                                <NavLink
                                    :href="route('apartments.index')"
                                    :active="
                                        route().current('apartments.index')
                                    "
                                    v-if="
                                        $page.props.auth.user.can.includes(
                                            'show apartments'
                                        )
                                    "
                                >
                                    {{ __("Apartments") }}
                                </NavLink>

                                <NavLink
                                    :href="route('finance.index')"
                                    :active="route().current('finance.index')"
                                    v-if="
                                        $page.props.auth.user.can.includes(
                                            'show finance'
                                        )
                                    "
                                >
                                    {{ __("Finance and Tax") }}
                                </NavLink>

                                <NavLink
                                    :href="route('statistics.index')"
                                    :active="
                                        route().current('statistics.index')
                                    "
                                    v-if="
                                        $page.props.auth.user.can.includes(
                                            'show statistics'
                                        )
                                    "
                                >
                                    {{ __("Statistics") }}
                                </NavLink>

                                <NavLink
                                    :href="route('users.index')"
                                    :active="route().current('users.index')"
                                    v-if="
                                        $page.props.auth.user.can.includes(
                                            'show users'
                                        )
                                    "
                                >
                                    {{ __("Users") }}
                                </NavLink>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <LanguageSwitcher />
                            <div
                                class="hidden lg:flex lg:items-center"
                                :class="[
                                    $page.props.locale.dir == 'ltr'
                                        ? 'lg:ml-6'
                                        : 'lg:mr-6',
                                ]"
                            >
                                <!-- Settings Dropdown -->
                                <div
                                    class="ml-3 relative"
                                    :class="[
                                        $page.props.locale.dir == 'ltr'
                                            ? 'ml-3'
                                            : 'mr-3',
                                    ]"
                                >
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                            <span
                                                class="inline-flex rounded-md"
                                            >
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                                >
                                                    {{
                                                        $page.props.auth.user
                                                            .name
                                                    }}
                                                    <svg
                                                        class="h-4 w-4"
                                                        :class="[
                                                            $page.props.locale
                                                                .dir == 'ltr'
                                                                ? 'ml-2 -mr-0.5'
                                                                : 'mr-2 -ml-0.5',
                                                        ]"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
                                        <template #content>
                                            <DropdownLink
                                                :href="route('profile.edit')"
                                            >
                                                {{ __("Profile") }}
                                            </DropdownLink>
                                            <DropdownLink
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                            >
                                                {{ __("Log Out") }}
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div
                            class="flex items-center lg:hidden"
                            :class="[
                                $page.props.locale.dir == 'ltr'
                                    ? '-mr-2'
                                    : '-ml-2',
                            ]"
                        >
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="lg:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'show apartments'
                                )
                            "
                        >
                            {{ __("Dashboard") }}
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('reservations.index')"
                            :active="route().current('reservations.index')"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'show reservations'
                                )
                            "
                        >
                            {{ __("Reservations") }}
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('calendar.index')"
                            :active="route().current('calendar.index')"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'show calendar'
                                )
                            "
                        >
                            {{ __("Calendar") }}
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('apartments.index')"
                            :active="route().current('apartments.index')"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'show apartments'
                                )
                            "
                        >
                            {{ __("Apartments") }}
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('finance.index')"
                            :active="route().current('finance.index')"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'show finance'
                                )
                            "
                        >
                            {{ __("Finance and Tax") }}
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('statistics.index')"
                            :active="route().current('statistics.index')"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'show statistics'
                                )
                            "
                        >
                            {{ __("Statistics") }}
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('users.index')"
                            :active="route().current('users.index')"
                            v-if="
                                $page.props.auth.user.can.includes('show users')
                            "
                        >
                            {{ __("Users") }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ __("Profile") }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                {{ __("Log Out") }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
