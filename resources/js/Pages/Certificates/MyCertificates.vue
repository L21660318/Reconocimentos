<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiClipboardTextClock } from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";

const props = defineProps({
    title: String,
    certificates: Array,
});
</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiClipboardTextClock" :title="title" main />

        <table v-if="certificates.length" class="table-auto w-full mt-6">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Tipo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cert in certificates" :key="cert.id">
                    <td>{{ cert.event.nombre }}</td>
                    <td>{{ cert.tipo }}</td>
                    <td>
                        <BaseButton
                            label="Ver"
                            color="info"
                            :to="route('certificate.preview', [cert.event_id, cert.user_id])"
                            target="_blank"
                            small
                        />
                        <BaseButton
                            label="Descargar"
                            color="success"
                            :to="route('certificate.download', [cert.event_id, cert.user_id])"
                            small
                        />
                    </td>
                </tr>
            </tbody>
        </table>

        <p v-else class="text-center text-gray-500 mt-6">No tienes certificados disponibles.</p>
    </LayoutMain>
</template>
