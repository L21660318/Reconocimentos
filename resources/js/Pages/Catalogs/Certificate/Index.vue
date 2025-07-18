<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiCertificate } from '@mdi/js';

const props = defineProps({
  events: Array,
  title: String
});
</script>

<template>
  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiCertificate" :title="title" main />
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <CardBox v-for="event in events" :key="event.id">
        <h2 class="font-bold text-lg">{{ event.nombre }}</h2>
        <p class="text-sm text-gray-500 mb-2">{{ event.tipo }}</p>
        <p class="text-sm">Participantes: {{ event.users_count }}</p>
        <BaseButton class="mt-4" color="info" :label="'Ver participantes'" :href="route('certificate.show', event.id)" />
      </CardBox>
    </div>
  </LayoutMain>
</template>