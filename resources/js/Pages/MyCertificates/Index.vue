<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import { mdiClipboardTextClock } from "@mdi/js";

const props = defineProps({
    certificates: Array,
    title: String,
});
</script>

<template>
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiClipboardTextClock" :title="title" main />

        <table v-if="certificates.length" class="table-auto w-full">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Descargar</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cert in certificates" :key="cert.id">
                    <td>{{ cert.event.nombre }}</td>
                    <td>{{ cert.tipo }}</td>
                    <td>{{ new Date(cert.created_at).toLocaleDateString() }}</td>
                    <td>
                        <a
                            :href="route('certificate.download', [cert.event_id, cert.user_id])"
                            target="_blank"
                            class="text-blue-600 hover:underline"
                        >
                            Descargar
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        <p v-else class="text-gray-500 text-center">No tienes certificados disponibles.</p>
    </LayoutMain>
</template>
