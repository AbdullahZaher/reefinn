<script setup>
import { computed, ref } from "vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Loader from "@/Components/Loader.vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import Swal from "sweetalert2";
import { __ } from "@/Composables/translations";

const props = defineProps({
    hotel: {
        type: Object,
    },
    timezones: {
        type: Array,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    _method: "PATCH",
    hotel_name: props.hotel.hotel_name,
    branch_no: props.hotel.branch_no,
    phone: props.hotel.phone,
    commercial_register: props.hotel.commercial_register,
    tax_number: props.hotel.tax_number,
    checkout_default_time: props.hotel.checkout_default_time,
    auto_renew_after: props.hotel.auto_renew_after.toString(),
    timezone: props.hotel.timezone,
    location: props.hotel.location,
});

// Logo
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize,
    FilePondPluginImagePreview
);

const logoElement = ref(null);
const logoPondFiles = ref(props.hotel.logo ? [props.hotel.logo] : []);

const submitHandler = () => {
    form.transform((data) => {
        const file = logoElement.value.getFiles()[0];

        return file && file.source !== props.hotel.logo
            ? {
                  ...data,
                  logo: file.file,
              }
            : data;
    }).post(route("hotel.update"), {
        preserveScroll: true,
        onSuccess: () =>
            (logoPondFiles.value = props.hotel.logo ? [props.hotel.logo] : []),
    });
};

const deleteLogo = () => {
    Swal.fire({
        title: __("Are you sure?"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: __("Cancel"),
        confirmButtonText: __("Yes, remove it!"),
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        backdrop: true,
        preConfirm: () =>
            new Promise((resolve) => {
                router.delete(route("hotel.logo.destroy"), {
                    preserveScroll: true,
                    onSuccess: () => {
                        logoPondFiles.value = props.hotel.logo
                            ? [props.hotel.logo]
                            : [];
                        resolve();
                    },
                });
            }),
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ __("Hotel Information") }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __("Update hotel's name and details.") }}
            </p>
        </header>

        <form @submit.prevent="submitHandler" class="mt-6 space-y-6">
            <div>
                <InputLabel
                    for="hotel_name"
                    :value="__('Hotel Name')"
                    required
                />

                <TextInput
                    id="hotel_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.hotel_name"
                    required
                />

                <InputError class="mt-2" :message="form.errors.hotel_name" />
            </div>

            <div>
                <InputLabel for="logo" :value="__('Logo')" />

                <FilePond
                    ref="logoElement"
                    label="Image"
                    acceptedFileTypes="image/jpeg, image/png"
                    maxFileSize="2MB"
                    :labelIdle="
                        __('Click to browse or drag & drop image here...')
                    "
                    :instantUpload="false"
                    :allowProcess="false"
                    :files="logoPondFiles"
                    :error="form.errors.image"
                />

                <button
                    type="button"
                    class="text-red-600 font-bold text-sm w-full"
                    @click="deleteLogo"
                    v-if="props.hotel.logo"
                >
                    {{ __("Delete Logo") }}
                </button>

                <InputError class="mt-2" :message="form.errors.logo" />
            </div>

            <div>
                <InputLabel
                    for="branch_no"
                    :value="__('Branch Number')"
                    required
                />

                <TextInput
                    id="branch_no"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.branch_no"
                />

                <InputError class="mt-2" :message="form.errors.branch_no" />
            </div>

            <div>
                <InputLabel for="phone" :value="__('Phone Number')" required />

                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div>
                <InputLabel
                    for="commercial_register"
                    :value="__('Commercial Register')"
                    required
                />

                <TextInput
                    id="commercial_register"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.commercial_register"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.commercial_register"
                />
            </div>

            <div>
                <InputLabel
                    for="tax_number"
                    :value="__('Tax Number')"
                    required
                />

                <TextInput
                    id="tax_number"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.tax_number"
                />

                <InputError class="mt-2" :message="form.errors.tax_number" />
            </div>

            <div>
                <InputLabel
                    for="checkout_default_time"
                    :value="__('Default Checkout Time')"
                    required
                />

                <TextInput
                    id="checkout_default_time"
                    type="time"
                    class="mt-1 block w-full"
                    v-model="form.checkout_default_time"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.checkout_default_time"
                />
            </div>

            <div>
                <InputLabel
                    for="auto_renew_after"
                    :value="__('Auto Renew After')"
                    required
                />

                <TextInput
                    id="auto_renew_after"
                    type="number"
                    min="0"
                    max="23"
                    class="mt-1 block w-full"
                    v-model="form.auto_renew_after"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.auto_renew_after"
                />
            </div>

            <div>
                <InputLabel
                    for="hotel-timezones"
                    :value="__('Timezone')"
                    required
                />

                <select
                    id="hotel-timezones"
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
                <InputLabel for="location" :value="__('Location')" required />

                <TextInput
                    id="location"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.location"
                />

                <InputError class="mt-2" :message="form.errors.location" />
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

<style>
.filepond--action-remove-item,
.filepond--credits {
    display: none;
}
</style>
