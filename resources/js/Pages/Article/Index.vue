<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
    mdiBallotOutline,
    mdiInformation,
    mdiPencil,
    mdiFilePdfBox,
    mdiBroom,
    mdiMagnify,
    mdiPlus,
    mdiEye,
    mdiCheckDecagram,
    mdiReceiptText
} from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import { router, Head, usePage } from "@inertiajs/vue3";
import Pagination from "@/Shared/Pagination.vue";
import { reactive, ref, provide } from "vue";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import Dropdown from "@/Components/DropdownTable.vue";
import HeadLogo from "@/Components/HeadLogo.vue";
import { limitText } from "@/Hooks/useFormato";
import { useCan } from "@/Hooks/usePermissions";

const props = defineProps({
    name: "Index",
    title: {
        type: String,
        required: true,
    },
    articles: {
        type: Object,
        default: () => ({}),
        required: true,
    },
    isReviewer: {
        type: Boolean,
        required: true
    },
    routeName: {
        type: String,
        required: true,
    },
    search: { type: String, required: true },
    direction: { type: String, required: true },
});

const search = ref(props.search);
const isLoading = ref(false);

const state = reactive({
    filters: {
        search: search,
        order: props.articles.order ?? "created_at",
        direction: props.direction,
    },
});

const filterSearch = () => {
    router.get(
        route(`${props.routeName}index`, state.filters, {
            replace: true,
        })
    );
};

const cleanFilters = () => {
    isLoading.value = true;
    router.get(route(`${props.routeName}index`));
};

const filterBy = (order, direction) => {
    state.filters.order = order;
    state.filters.direction = direction;
    isLoading.value = true;
    router.get(route(`${props.routeName}index`, state.filters));
};

const opts = [
    {
        label: "Titulo",
        key: "title",
        menu: [
            { title: "Ordenar desde A - Z", direction: "asc" },
            { title: "Ordenar desde Z - A", direction: "desc" },
        ],
    },
    {
        label: "Tipo",
        key: "type",
        menu: [
            { title: "Ordenar desde A - Z", direction: "asc" },
            { title: "Ordenar desde Z - A", direction: "desc" },
        ],
    },
    {
        label: "Resumen",
        key: "abstract",
        menu: [
            { title: "Ordenar desde A - Z", direction: "asc" },
            { title: "Ordenar desde Z - A", direction: "desc" },
        ],
    },
    {
        label: "Area",
        key: "area",
        menu: [
            { title: "Ordenar desde A - Z", direction: "asc" },
            { title: "Ordenar desde Z - A", direction: "desc" },
        ],
    },
    {
        label: "Estatus",
        key: "status",
        menu: [
            { title: "Ordenar desde A - Z", direction: "asc" },
            { title: "Ordenar desde Z - A", direction: "desc" },
        ],
    },
];

const canEdit = (id) => {
    if (useCan('article.update')) {
        const article = props.articles.data.find(article => article?.id === id);
        if (article.isPostulant && article.status.can_edit) {
            return true;
        }
    }
    return false;
}

provide("filterBy", filterBy);

</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiBallotOutline" :title="title" main>
        </SectionTitleLineWithButton>

        <NotificationBar v-if="$page.props.flash.success" color="success" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.success }}
        </NotificationBar>

        <NotificationBar v-if="$page.props.flash.error" color="danger" :icon="mdiInformation" :outline="false">
            {{ $page.props.flash.error }}
        </NotificationBar>

        <form @submit.prevent="filterSearch" class="w-full mb-5">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="flex w-full md:w-1/2 mr-1 my-4">
                    <input type="search" id="search-dropdown"
                        class="block pr-10 md:h-11 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg md:border-l-gray-300 border-l-gray-300 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-l-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                        placeholder="Ingresa un parametro de busqueda" v-model="search" />
                    <BaseButton class="relative z-30 right-0 h-11 rounded-none" @click="filterSearch" :icon="mdiMagnify"
                        color="info" title="Buscar" />
                    <BaseButton class=" relative right-0 h-11 rounded-l-none rounded-r-lg" @click="cleanFilters"
                        :icon="mdiBroom" color="lightDark" title="Limpiar filtro" />
                </div>

                <BaseButtons>
                    <BaseButton v-if="useCan('article.store')" :routeName="`${routeName}create`" color="info"
                        :icon="mdiPlus" label="Agregar artículo" class="w-full" />
                </BaseButtons>
            </div>
        </form>

        <CardBox v-if="articles.data.length > 0">
            <table>
                <thead>
                    <tr>
                        <th />

                        <th>
                            <Dropdown title="Titulo" :options="opts" />
                        </th>
                        <th>
                            <Dropdown title="Tipo" :options="opts" />
                        </th>
                        <th>
                            <Dropdown title="Resumen" :options="opts" />
                        </th>
                        <th>
                            <Dropdown title="Area" :options="opts" />
                        </th>
                        <th class="text-sm" v-if="isReviewer">
                            Estatus de evaluación
                        </th>
                        <th v-else>
                            <Dropdown title="Estatus" :options="opts" />
                        </th>
                        <th>Comprobante</th>
                        <th class="relative inline-block text-left">
                            <span
                                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-bold text-gray-900 dark:text-white ring-inset ring-gray-300 hover:bg-gray-100">
                                Acciones
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in articles.data" :key="item.id">
                        <td class="align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-book-half" viewBox="0 0 16 16">
                                <path
                                    d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z" />
                            </svg>
                        </td>
                        <td data-label="Titulo">
                            {{ limitText(item.title, 50) }}
                        </td>
                        <td data-label="Tipo">
                            {{ item.type }}
                        </td>
                        <td data-label="Resumen">
                            {{ limitText(item.abstract, 50) }}
                        </td>
                        <td data-label="Area">
                            {{ item.area }}
                        </td>
                        <td v-if="isReviewer" data-label="Estatus de evaluación">
                            <div class="dark:bg-green-800 dark:text-green-200 bg-green-100 rounded-full text-green-500 px-2 dark:opacity-95 opacity-85 w-max text-center"
                                v-if="item.statusReviewer">
                                Evaluado
                            </div>
                            <div v-else
                                class="dark:bg-yellow-800 dark:text-yellow-200 bg-yellow-100 rounded-full text-yellow-500 px-2 dark:opacity-95 opacity-85 w-max text-center"
                                v-if="!item.statusReviewer">
                                Pendiente de evaluar
                            </div>
                        </td>
                        <td v-else data-label="Estatus">
                            <div :class="item.status.class">
                                {{ item.status.name }}
                            </div>
                        </td>
                        <td data-label="Comprobante">
                            {{ item.paymentVoucherStatus?.name }}
                        </td>
                        <td data-label="Acciones">
                            <BaseButtons>
                                <BaseButton v-if="canEdit(item.id)" color="info" :icon="mdiPencil" small
                                    :routeName="`${routeName}edit`" :parameter="item.id" title="Editar artículo" />
                                <BaseButton color="info" :icon="mdiCheckDecagram" iconSize="22" small
                                    :routeName="`${routeName}show`" :parameter="item.id" title="Ver artículo" />
                            </BaseButtons>
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardBox>
        <CardBoxComponentEmpty v-else />
        <pagination :links="articles.links" :total="articles.total" :to="articles.to" :from="articles.from" />
        <div class="vl-parent">
            <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
        </div>
    </LayoutMain>
</template>
