<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import EditApartmentModal from "@/Components/Modals/EditApartmentModal.vue";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    apartment: {
        type: Object,
    },
    apartmentTypes: {
        type: Object,
    },
    apartmentDescriptions: {
        type: Object,
    },
});

const isEditApartmentModalOpen = ref(false);

const updateMaintenance = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, change it!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.patch(
                    route("apartments.update.maintenance", props.apartment.id),
                    {},
                    {
                        preserveScroll: true,
                        onSuccess: resolve,
                    }
                );
            }),
    });
};

const deleteApartment = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, delete apartment!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.delete(route("apartments.destroy", props.apartment.id), {
                    preserveScroll: true,
                    onFinish: resolve,
                });
            }),
    });
};
</script>

<template>
    <EditApartmentModal
        :apartmentTypes="apartmentTypes"
        :apartmentDescriptions="apartmentDescriptions"
        :open="isEditApartmentModalOpen"
        @close="isEditApartmentModalOpen = false"
        :apartment="apartment"
        v-if="
            isEditApartmentModalOpen &&
            $page.props.auth.user.can.includes('edit apartments')
        "
    />

    <tr class="bg-white border-b">
        <th
            scope="row"
            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
        >
            {{ apartment.id }}
        </th>
        <td class="px-6 py-4">
            <span
                class="px-3 py-1 rounded-xl text-white whitespace-nowrap"
                :class="`bg-${apartment.state_color}-500`"
            >
                {{ __(apartment.state) }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ apartment.name }}
        </td>
        <td class="px-6 py-4 max-w-xs break-words">
            {{ __(apartment.type) }}
        </td>
        <td class="px-6 py-4 max-w-xs break-words">
            {{ __(apartment.description) }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            {{ apartment.price_for_night + " " + __("SAR") }}
        </td>
        <td class="px-6 py-4 break-all">
            {{ apartment.note ?? "-" }}
        </td>
        <td
            class="px-6 py-4 flex items-center justify-center gap-5 whitespace-nowrap"
        >
            <button
                @click="updateMaintenance"
                :title="__('Change to maintenance state')"
                v-if="
                    $page.props.auth.user.can.includes(
                        'maintenance apartments'
                    ) && apartment.state.toLowerCase() != 'maintenance'
                "
            >
                <FontAwesomeIcon icon="fas fa-wrench" />
            </button>

            <button
                @click="updateMaintenance"
                :title="__('Finish Maintenance')"
                v-if="
                    $page.props.auth.user.can.includes(
                        'maintenance apartments'
                    ) && apartment.state.toLowerCase() == 'maintenance'
                "
            >
                <FontAwesomeIcon icon="fas fa-unlock" />
            </button>

            <button
                @click="isEditApartmentModalOpen = true"
                :title="__('Edit Apartment')"
                v-if="$page.props.auth.user.can.includes('edit apartments')"
            >
                <FontAwesomeIcon icon="fas fa-pen-to-square" />
            </button>

            <button
                @click="deleteApartment"
                :title="__('Delete Apartment')"
                v-if="$page.props.auth.user.can.includes('delete apartments')"
            >
                <FontAwesomeIcon icon="fas fa-trash" />
            </button>

            <span
                v-if="
                    !$page.props.auth.user.can.includes(
                        'maintenance apartments'
                    ) &&
                    !$page.props.auth.user.can.includes('edit apartments') &&
                    !$page.props.auth.user.can.includes('delete apartments')
                "
            >
                -
            </span>
        </td>
    </tr>
</template>
