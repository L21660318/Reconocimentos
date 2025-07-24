<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import BaseButton from "@/Components/BaseButton.vue";
import { mdiClipboardCheck } from "@mdi/js";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    title: String,
    event: Object,
    requests: Array,
});

const aceptar = (id) => {
    router.post(route('event.requests.accept', id));
};

const rechazar = (id) => {
    router.post(route('event.requests.reject', id));
};
</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiClipboardCheck" :title="title" main />

        <table v-if="requests.length" class="table-auto w-full mt-4">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Status</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="req in requests" :key="req.id">
                    <td>{{ req.user.name }}</td>
                    <td>{{ req.user.email }}</td>
                    <td>{{ req.status }}</td>
                    <td>
                        <BaseButton label="Aceptar" color="success" @click="aceptar(req.id)" />
                        <BaseButton label="Rechazar" color="danger" @click="rechazar(req.id)" />
                    </td>
                </tr>
            </tbody>
        </table>

        <p v-else class="text-center text-gray-500">No hay solicitudes para este evento.</p>
    </LayoutMain>
</template>
