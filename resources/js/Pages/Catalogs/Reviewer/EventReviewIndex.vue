<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import { mdiClipboardTextClock } from "@mdi/js";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    title: String,
    events: Array,
});
</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiClipboardTextClock" :title="title" main />

        <CardBox v-if="events.length">
            <table>
                <thead>
                    <tr>
                        <th>Nombre del evento</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="event in events" :key="event.id">
                        <td>{{ event.nombre }}</td>
                        <td>{{ event.pivot?.tipo || '-' }}</td>
                        <td>{{ event.pivot?.estatus || 'pendiente' }}</td>
                        <td>
                            <BaseButton
                                color="info"
                                label="Revisar"
                                :href="route('event-review.edit', event.id)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </CardBox>
        <p v-else class="text-center text-gray-500 dark:text-gray-300 py-4">
            No hay eventos pendientes de revisión.
        </p>
    </LayoutMain>
</template>
