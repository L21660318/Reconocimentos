<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiCalendar, mdiInformation, mdiPencil, mdiBroom, mdiMagnify, mdiPlus } from "@mdi/js";
import CardBox from "@/Components/CardBox.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import Pagination from "@/Shared/Pagination.vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import { reactive, ref, provide } from "vue";
import { router } from "@inertiajs/vue3";
import Dropdown from "@/Components/DropdownTable.vue";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    title: String,
    events: Object,
    routeName: String,
    search: String,
    direction: String,
});

const search = ref(props.search);
const isLoading = ref(false);
const state = reactive({
    filters: {
        search: search,
        order: 'created_at',
        direction: props.direction,
    },
});

const filterSearch = () => {
    router.get(route(`${props.routeName}index`, state.filters), { replace: true });
};

const cleanFilters = () => {
    isLoading.value = true;
    router.get(route(`${props.routeName}index`));
};

const opts = [
    {
        label: "Nombre",
        key: "nombre",
        menu: [
            { title: "A - Z", direction: "asc" },
            { title: "Z - A", direction: "desc" },
        ],
    },
    {
        label: "Tipo",
        key: "tipo",
        menu: [
            { title: "A - Z", direction: "asc" },
            { title: "Z - A", direction: "desc" },
        ],
    },
];

provide("filterBy", (order, direction) => {
    state.filters.order = order;
    state.filters.direction = direction;
    isLoading.value = true;
    router.get(route(`${props.routeName}index`, state.filters));
});
</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiCalendar" :title="title" main />

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <form @submit.prevent="filterSearch" class="mb-5">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="flex w-full md:w-1/2">
                    <input type="search" v-model="search" placeholder="Buscar evento..." class="input-search" />
                    <BaseButton :icon="mdiMagnify" @click="filterSearch" color="info" />
                    <BaseButton :icon="mdiBroom" @click="cleanFilters" color="lightDark" />
                </div>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}create`" :icon="mdiPlus" color="info" label="Agregar evento" />
                </BaseButtons>
            </div>
        </form>

        <CardBox v-if="events.data.length">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="event in events.data" :key="event.id">
                        <td>{{ event.nombre }}</td>
                        <td>{{ event.tipo }}</td>
                        <td>{{ event.fecha_inicio }}</td>
                        <td>{{ event.fecha_fin }}</td>
                        <td>
                            <BaseButtons>
                                <BaseButton :icon="mdiPencil" color="info" :routeName="`${routeName}edit`" :parameter="event.id" />
                                <BaseButton
                                    :routeName="`${routeName}assignUsers`"
                                    :parameter="event.id"
                                    color="info"
                                    label="Asignar usuarios"
                                />
                                <a
                                :href="route('event.requests', event.id)"
                                target="_blank"
                                class="btn btn-info text-sm px-2 py-1 rounded"
                                >
                                Ver solicitudes
                                </a>

                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardBox>

        <CardBoxComponentEmpty v-else />
        <Pagination v-bind="events" />
        <Loading v-model:active="isLoading" :is-full-page="true" />
    </LayoutMain>
</template>
