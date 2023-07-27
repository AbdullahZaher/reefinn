<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import UserItem from "@/Components/UserItem.vue";
import CreateUserModal from "@/Components/Modals/CreateUserModal.vue";
import InfiniteScroll from "@/Components/InfiniteScroll/InfiniteScroll.vue";
import useInfiniteScroll from "@/Composables/useInfiniteScroll.js";
import { __ } from "@/Composables/translations";

const props = defineProps({
    filters: {
        type: Object,
    },
    users: {
        type: Object,
    },
    permissions: {
        type: Array,
    },
});

const users = ref(props.users);

watch(
    () => props.users,
    (value) => (users.value = value)
);

const loadMore = () => useInfiniteScroll(users).loadMore();

const isCreateUserModalOpen = ref(false);
</script>

<template>
    <Head :title="__('Users')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Users") }}
            </h2>
        </template>

        <CreateUserModal
            :permissions="permissions"
            :open="isCreateUserModalOpen"
            @close="isCreateUserModalOpen = false"
            v-if="
                isCreateUserModalOpen &&
                $page.props.auth.user.can.includes('create users')
            "
        />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-4 py-12">
                    <div
                        class="text-center pb-12 flex items-center justify-between flex-col lg:flex-row space-y-6 lg:space-y-0"
                    >
                        <h1
                            class="font-bold text-5xl font-heading text-gray-900"
                        >
                            {{ __("Users") }}
                        </h1>
                        <button
                            type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 w-full lg:w-32"
                            @click="isCreateUserModalOpen = true"
                            v-if="
                                $page.props.auth.user.can.includes(
                                    'create users'
                                )
                            "
                        >
                            {{ __("Add") }}
                        </button>
                    </div>

                    <div
                        class="relative overflow-x-auto shadow-md sm:rounded-lg"
                    >
                        <InfiniteScroll :loadMore="loadMore">
                            <table
                                class="w-full text-sm text-left rtl:text-right text-gray-500"
                            >
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50"
                                >
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("ID") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Name") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Email") }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __("Actions") }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <UserItem
                                        v-for="user in users.data"
                                        :key="user.id"
                                        :user="user"
                                        :permissions="permissions"
                                        :onlyOne="users.data.length === 1"
                                    />
                                </tbody>
                            </table>
                        </InfiniteScroll>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
