<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import UpdateHotelInformationForm from "./Partials/UpdateHotelInformationForm.vue";
import UpdateReservationLeaseTermsForm from "./Partials/UpdateReservationLeaseTermsForm.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    hotel: {
        type: Object,
    },
    timezones: {
        type: Array,
    },
    calendars: {
        type: Array,
    },
    terms: {
        type: Object,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"
                    v-if="
                        $page.props.auth.user.can.includes(
                            'edit hotel information'
                        )
                    "
                >
                    <UpdateHotelInformationForm
                        :hotel="hotel"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdateProfileInformationForm
                        :timezones="timezones"
                        :calendars="calendars"
                        class="max-w-xl"
                    />
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="p-4 sm:p-8 bg-white shadow sm:rounded-lg"
                    v-if="
                        $page.props.auth.user.can.includes(
                            'edit terms of the reservation lease'
                        )
                    "
                >
                    <UpdateReservationLeaseTermsForm :terms="terms" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
