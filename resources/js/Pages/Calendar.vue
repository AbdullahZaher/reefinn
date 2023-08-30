<script setup>
import { computed, ref, watch } from "vue";
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import Dropdown from "@/Components/Dropdown/Dropdown.vue";
import DropdownItem from "@/Components/Dropdown/DropdownItem.vue";
import AddReservationAvailableApartmentsModal from "@/Components/Modals/AddReservationAvailableApartmentsModal.vue";
import EditReservationModal from "@/Components/Modals/EditReservationModal.vue";
import { __ } from "@/Composables/translations";
import { throttle } from "lodash-es";
import Swal from "sweetalert2";

const props = defineProps({
    filters: {
        type: Object,
    },
    apartments: {
        type: Object,
    },
    reservations: {
        type: Object,
    },
    idTypes: {
        type: Object,
    },
    paymentMethods: {
        type: Object,
    },
});

const reservations = ref(props.reservations.data);

// Filtering
const filters = ref({
    apartments: props.filters.apartments,
});

watch(
    filters,
    throttle((filters) => {
        router.get(
            route("calendar.index"),
            {
                apartments: filters.apartments.join(","),
            },
            {
                preserveState: true,
                only: ["reservations", "filters"],
            }
        );
    }, 250),
    { deep: true }
);

const onlyOneAparment = computed(
    () =>
        filters.value.apartments.length == 1 &&
        usePage().props.ziggy.params.only_one
);
const currentApartment = computed(() => {
    if (!onlyOneAparment.value) return null;

    return props.apartments.data[
        props.apartments.data.findIndex(
            (apartment) => apartment.id == filters.value.apartments[0]
        )
    ];
});

const events = computed(() =>
    reservations.value.map((reservation, index) => ({
        index,
        id: reservation.id,
        start: reservation.checkin,
        end: reservation.checkout,
        title: `${
            !onlyOneAparment.value ? reservation.apartment.name + " - " : ""
        }${__(reservation.guest_name)} (${__(reservation.state)})`,
        className: `text-white py-[0.1rem] px-3 hover:bg-${
            reservation.state_color
        }-500 bg-${reservation.state_color}-600  ${
            usePage().props.auth.user.can.includes("edit reservations")
                ? " cursor-pointer"
                : ""
        }`,
        allDay: true,
    }))
);

const isAddReservationModalOpen = ref(false);
const addReservationData = ref({
    checkin: null,
    checkout: null,
});

const isEditReservationModalOpen = ref(false);
const currentReservation = ref(null);

const handleDateSelect = (selectInfo) => {
    if (usePage().props.auth.user.can.includes("create reservations")) {
        isEditReservationModalOpen.value = false;

        addReservationData.value.checkin = selectInfo.startStr;

        const checkoutDate = new Date(selectInfo.endStr);
        checkoutDate.setDate(checkoutDate.getDate() - 1);
        addReservationData.value.checkout = checkoutDate
            .toISOString()
            .split("T")[0];

        selectInfo.view.calendar.unselect();

        isAddReservationModalOpen.value = true;
    }
};

const handleEventClick = (clickInfo) => {
    if (usePage().props.auth.user.can.includes("edit reservations")) {
        isAddReservationModalOpen.value = false;

        currentReservation.value =
            reservations.value[clickInfo.event.extendedProps.index];

        isEditReservationModalOpen.value = true;
    }
};

const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin, listPlugin],
    events: events.value,
    locale: usePage().props.locale.lang,
    direction: usePage().props.locale.dir,
    firstDay: 6,
    buttonText: {
        today: __("Today"),
        list: __("List"),
        month: __("Month"),
    },
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: window.innerWidth >= 765 ? "dayGridMonth,listMonth" : "",
    },
    noEventsContent: __("No reservations found"),
    initialView: window.innerWidth >= 765 ? "dayGridMonth" : "listMonth",
    dayMaxEvents: true,
    views: {
        dayGridMonth: {
            dayMaxEvents: 10,
        },
    },
    moreLinkContent: (args) => `+${args.num} ${__("more reservations")}`,
    selectable: true,
    select: handleDateSelect,
    selectAllow: (selectInfo) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        return selectInfo.start >= today;
    },
    eventClick: handleEventClick,
});

watch(
    () => props.reservations.data,
    (newReservations) => {
        reservations.value = newReservations;
        calendarOptions.value.events = events.value;
    }
);

// iCalendar
const icalendarApartmentIndex = ref(null);

const getiCalendarLink = () => {
    if (icalendarApartmentIndex.value == null) {
        return Swal.fire({
            icon: "error",
            title: __("Sorry..."),
            text: __("Please select an apartment!"),
            confirmButtonText: __("Ok"),
        });
    }

    const url = new URL(
        route(
            "apartments.icalendar",
            props.apartments.data[icalendarApartmentIndex.value].id
        )
    );

    Swal.fire({
        icon: "success",
        title: __("Success"),
        text: __("iCalendar link copied to clipboard!"),
        confirmButtonText: __("Ok"),
    });

    const input = document.createElement("textarea");
    input.innerHTML = url.href;
    document.body.appendChild(input);
    input.select();
    document.execCommand("copy");
    document.body.removeChild(input);
};
</script>

<template>
    <Head :title="__('Calendar')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __("Calendar") }}
            </h2>
        </template>

        <AddReservationAvailableApartmentsModal
            :apartment="onlyOneAparment ? currentApartment : null"
            :reservation="addReservationData"
            :idTypes="idTypes"
            :paymentMethods="paymentMethods"
            :open="isAddReservationModalOpen"
            @close="isAddReservationModalOpen = false"
            v-if="
                isAddReservationModalOpen &&
                $page.props.auth.user.can.includes('create reservations')
            "
        />

        <EditReservationModal
            :apartment="currentReservation.apartment"
            :reservation="currentReservation"
            :idTypes="idTypes"
            :paymentMethods="paymentMethods"
            :open="isEditReservationModalOpen"
            @close="isEditReservationModalOpen = false"
            v-if="
                isEditReservationModalOpen &&
                $page.props.auth.user.can.includes('edit reservations')
            "
        />

        <div class="py-12">
            <div class="max-w-[100rem] mx-auto sm:px-6 lg:px-8">
                <div class="px-4 sm:px-6 lg:px-4 py-12">
                    <div
                        class="pb-12 flex items-center justify-between flex-col lg:flex-row space-y-6 lg:space-y-0"
                    >
                        <div class="space-y-10">
                            <h1 class="font-bold text-5xl text-gray-900">
                                {{ __("Calendar") }}
                            </h1>
                            <h3
                                class="font-medium text-3xl text-gray-900"
                                v-if="onlyOneAparment"
                            >
                                <span class="font-bold">
                                    {{ __("Apartment") }}:
                                </span>
                                {{ currentApartment.name }}
                            </h3>
                        </div>
                        <Dropdown
                            :title="__('Apartments')"
                            icon="fa-solid fa-microchip"
                            v-if="!onlyOneAparment"
                        >
                            <DropdownItem
                                v-for="(apartment, index) in apartments.data"
                                :key="index"
                                :title="__(apartment.name)"
                                :value="apartment.id"
                                v-model="filters.apartments"
                            />
                        </Dropdown>
                    </div>

                    <FullCalendar :options="calendarOptions">
                        <template v-slot:eventContent="arg">
                            <b>{{ arg.timeText }}</b>
                            <i>{{ arg.event.title }}</i>
                        </template>
                    </FullCalendar>
                </div>

                <div
                    class="my-10 bg-white border border-gray-400 rounded-xl mx-2"
                >
                    <template v-if="onlyOneAparment">
                        <div class="p-5 text-2xl font-bold text-center">
                            {{ __("iCalendar Link") }}
                        </div>
                        <div
                            class="p-5 text-center flex items-center justify-center underline text-xl border-t border-gray-400"
                        >
                            <a
                                :href="
                                    route(
                                        'apartments.icalendar',
                                        currentApartment.id
                                    )
                                "
                                target="_blank"
                                class="break-all"
                            >
                                {{
                                    route(
                                        "apartments.icalendar",
                                        currentApartment.id
                                    )
                                }}
                            </a>
                        </div>
                    </template>

                    <template v-else>
                        <div class="p-5 text-2xl font-bold text-center">
                            {{ __("Get iCalendar Link") }}
                        </div>

                        <div class="border-t border-gray-400">
                            <div
                                class="p-5 text-center flex items-stretch md:items-center gap-10 justify-center flex-col md:flex-row"
                            >
                                <div
                                    class="flex items-center gap-3 md:flex-1 w-full md:w-[20rem] flex-col md:flex-row mx-auto"
                                >
                                    <label
                                        for="apartments"
                                        class="block mb-2 text-sm font-medium text-gray-900 w-fit whitespace-nowrap"
                                    >
                                        {{ __("Select an apartment") }}
                                        <span class="text-red-600 text-sm"
                                            >*</span
                                        >
                                    </label>
                                    <select
                                        id="apartments"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 rtl md:flex-1 w-full md:w-auto"
                                        v-model="icalendarApartmentIndex"
                                    >
                                        <option :value="null" disabled selected>
                                            {{ __("Select an apartment") }}
                                        </option>
                                        <option
                                            v-for="(
                                                apartment, index
                                            ) in apartments.data"
                                            :key="apartment.id"
                                            :value="index"
                                        >
                                            {{ apartment.name }} ({{
                                                __(apartment.type)
                                            }})
                                        </option>
                                    </select>
                                </div>
                                <button
                                    class="inline-block px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue"
                                    @click="getiCalendarLink"
                                >
                                    {{ __("Get Link") }}
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.fc-list-event-time {
    display: none;
}

.fc .fc-list-event:hover td {
    background-color: inherit !important;
}

.fc-daygrid-event.fc-event {
    white-space: unset !important;
}

i {
    font-style: normal;
}

@media (max-width: 767px) {
    [dir="ltr"] .fc-header-toolbar {
        flex-direction: column !important;
        gap: 1.5rem;
    }

    [dir="rtl"] .fc-header-toolbar {
        flex-direction: column-reverse !important;
        gap: 1.5rem;
    }
}
</style>
