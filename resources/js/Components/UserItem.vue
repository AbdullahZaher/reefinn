<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import EditUserModal from "@/Components/Modals/EditUserModal.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    user: {
        type: Object,
    },
    permissions: {
        type: Array,
    },
    onlyOne: {
        type: Boolean,
        default: false,
    },
});

const isEditUserModalOpen = ref(false);

const deleteUser = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, delete user!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.delete(route("users.destroy", props.user.id), {
                    preserveScroll: true,
                    onFinish: resolve,
                });
            }),
    });
};
</script>

<template>
    <EditUserModal
        :user="user"
        :permissions="permissions"
        :open="isEditUserModalOpen"
        @close="isEditUserModalOpen = false"
        v-if="
            isEditUserModalOpen &&
            $page.props.auth.user.can.includes('edit users')
        "
    />

    <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
        >
            {{ user.id }}
        </th>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ user.name }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ user.email }}
        </td>
        <td class="px-6 py-4 space-x-5 rtl:space-x-reverse whitespace-nowrap">
            <button
                type="button"
                @click="isEditUserModalOpen = true"
                v-if="$page.props.auth.user.can.includes('edit users')"
            >
                <FontAwesomeIcon icon="fas fa-pen-to-square" />
            </button>
            <button
                type="button"
                @click="deleteUser"
                v-if="
                    $page.props.auth.user.can.includes('delete users') &&
                    !onlyOne
                "
            >
                <FontAwesomeIcon icon="fas fa-trash" />
            </button>
            <span
                v-if="
                    !$page.props.auth.user.can.includes('edit users') &&
                    !$page.props.auth.user.can.includes('delete users') &&
                    onlyOne
                "
                >-</span
            >
        </td>
    </tr>
</template>
