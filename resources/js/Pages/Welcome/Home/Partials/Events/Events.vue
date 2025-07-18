<script setup>
import Card from './CardEvent.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from './ModalEvent.vue';
import { provide, ref } from 'vue';
import { mdiMagnify, mdiBroom } from '@mdi/js';
import BaseButton from '@/Components/BaseButton.vue';
import CardBoxComponentEmpty from '@/Components/CardBoxComponentEmpty.vue';
import { router } from "@inertiajs/vue3";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

const props = defineProps({
    events: {
        type: Object,
        required: true
    },
    search: { type: String, required: true },
});

const showModal = ref(false);
const search = ref(props.search);
const event = ref(null);
const isLoading = ref(false);

const setShowModal = (value) => {
    showModal.value = value;
};

const openModal = (item) => {
    event.value = item;
    setShowModal(true);
};

const getEvents = () => {
    isLoading.value = true;
    router.visit(route("welcome.events", { search: search.value }), {
        preserveScroll: true,
    });
};

const cleanFilters = () => {
    isLoading.value = true;
    router.visit(route("welcome.events"), {
        preserveScroll: true,
    });
};

provide('showModal', { showModal, setShowModal });
</script>

<template>
    <div class="vl-parent">
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
    </div>
    <section class="py-14 max-w-screen-xl mx-auto">
        <div class="max-w-screen-xl mx-auto px-4  text-gray-600 md:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <h3 class="text-gray-800 dark:text-blue-200 text-3xl font-semibold sm:text-4xl">
                    Nuestros eventos
                </h3>
                <p class="mt-3 dark:text-gray-400">
                    Conoce los próximos eventos académicos disponibles.
                </p>
            </div>
            <div class="w-full my-5 justify-center flex">
                <div class="w-1/2 flex flex-col md:flex-row justify-between">
                    <div class="mt-4 relative w-full md:mt-0">
                        <input type="search" id="search-dropdown"
                            class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-l-lg md:rounded-l-none rounded-r-lg md:border-l-gray-300 border-l-gray-300 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-l-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                            placeholder="Buscar evento..." v-model="search" @change="getEvents" />
                        <BaseButton class="absolute top-0 right-8 h-full rounded-none" @click.prevent="getEvents"
                            :icon="mdiMagnify" color="info" />
                        <BaseButton class="absolute top-0 right-0 h-full rounded-l-none rounded-r-lg"
                            @click="cleanFilters" :icon="mdiBroom" color="contrast" />
                    </div>
                </div>
            </div>
            <div v-if="events.data.length > 0"
                class="h-[600px] py-5 overflow-auto sm:overflow-hidden sm:h-auto justify-center gap-4 grid sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                <div v-for="event in events.data" :key="event.id" class="mb-2">
                    <Card @open-modal="openModal" :event="event" />
                </div>
            </div>
            <CardBoxComponentEmpty v-else />
            <Pagination :links="events.links" />
        </div>
    </section>

    <Modal :event="event" />
</template>