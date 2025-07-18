<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import FormField from '@/Components/FormField.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { router } from '@inertiajs/vue3';
import { mdiAccountSchool } from '@mdi/js';
import { ref, computed } from 'vue';

const props = defineProps({
  event: Object,
  title: String
});

const tipo = ref('Participación');
const selectedUsers = ref([]);
const search = ref('');

// Computed para eliminar duplicados y aplicar filtro
const uniqueUsers = computed(() => {
  const seen = new Set();
  return props.event.users.filter(user => {
    if (seen.has(user.id)) return false;
    seen.add(user.id);
    const fullName = user.name.toLowerCase();
    return fullName.includes(search.value.toLowerCase());
  });
});

const submit = () => {
  if (!Array.isArray(selectedUsers.value) || selectedUsers.value.length === 0) {
    alert('Selecciona al menos un usuario.');
    return;
  }

  const certificados = selectedUsers.value.map(user_id => ({
    user_id,
    tipo: tipo.value
  }));

  router.post(route('certificate.store', props.event.id), { certificados }, {
    onError: (errors) => {
      console.error(errors);
      alert('Error al generar certificados');
    }
  });
};
</script>

<template>
  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiAccountSchool" :title="title" main />

    <CardBox>
      <h2 class="text-lg font-semibold mb-4">{{ event.nombre }} ({{ event.tipo }})</h2>

      <FormField label="Tipo de certificado">
        <select v-model="tipo" class="form-select w-full">
          <option value="Participación">Participación</option>
          <option value="Reconocimiento">Reconocimiento</option>
          <option value="Constancia">Constancia</option>
        </select>
      </FormField>

      <FormField label="Buscar usuario">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por nombre..."
          class="form-input w-full"
        />
      </FormField>

      <div class="grid gap-2 mt-4">
        <div
          v-for="user in uniqueUsers"
          :key="user.id"
          class="flex items-center gap-4"
        >
          <input
            type="checkbox"
            :value="user.id"
            v-model="selectedUsers"
          />
          <div>
            <p class="font-medium">{{ user.name }}</p>
            <p class="text-sm text-gray-500">Área: {{ user.knowledge_area?.name ?? 'No asignada' }}</p>
          </div>
        </div>
      </div>

      <BaseButton
        class="mt-4"
        color="success"
        label="Generar certificados"
        @click="submit"
      />
    </CardBox>
  </LayoutMain>
</template>
