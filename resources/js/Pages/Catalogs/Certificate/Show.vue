<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import FormField from '@/Components/FormField.vue';
import { router } from '@inertiajs/vue3';
import { mdiAccountSchool, mdiSelectAll, mdiCheckboxBlankOutline, mdiEye } from '@mdi/js';
import { ref, computed, watch } from 'vue';

const props = defineProps({
  event: Object,
  title: String
});

const tipo = ref('Participación');
const selectedUsers = ref([]);
const search = ref('');
const previewUserId = ref(null);

// Elimina duplicados y filtra por nombre o correo
const uniqueUsers = computed(() => {
  const seen = new Set();
  return props.event.users.filter(user => {
    if (seen.has(user.id)) return false;
    seen.add(user.id);
    const term = search.value.toLowerCase();
    return user.name.toLowerCase().includes(term) || user.email.toLowerCase().includes(term);
  });
});

const checkAll = () => {
  const filteredIds = uniqueUsers.value.map(user => user.id);
  selectedUsers.value = Array.from(new Set([...selectedUsers.value, ...filteredIds]));
};

const uncheckAll = () => {
  const idsToRemove = new Set(uniqueUsers.value.map(user => user.id));
  selectedUsers.value = selectedUsers.value.filter(id => !idsToRemove.has(id));
};

const submit = () => {
  if (selectedUsers.value.length === 0) {
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

const openPreview = (userId) => {
  previewUserId.value = userId;
};

const closePreview = () => {
  previewUserId.value = null;
};

const previewUrl = computed(() => {
  return previewUserId.value
    ? route('certificate.preview', { event: props.event.id, user: previewUserId.value, tipo: tipo.value })
    : null;
});

// Detectar tecla ESC para cerrar el modal
const handleKeyDown = (e) => {
  if (e.key === 'Escape') {
    closePreview();
  }
};

// Agregar y remover el event listener dinámicamente
watch(previewUserId, (val) => {
  if (val) {
    document.addEventListener('keydown', handleKeyDown);
  } else {
    document.removeEventListener('keydown', handleKeyDown);
  }
});
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
          placeholder="Buscar por nombre o correo..."
          class="form-input w-full"
        />
      </FormField>

      <div class="flex gap-2 mt-2 mb-4">
        <BaseButton :icon="mdiSelectAll" color="info" label="Marcar todos" @click="checkAll" />
        <BaseButton :icon="mdiCheckboxBlankOutline" color="warning" label="Desmarcar todos" @click="uncheckAll" />
      </div>

      <div class="grid gap-2 max-h-[60vh] overflow-y-auto">
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
            <p class="text-sm text-gray-500">
              {{ user.email }}<br>
              Área: {{ user.knowledge_area?.name ?? 'No asignada' }}
            </p>
          </div>

          <BaseButton
            color="info"
            label="Descargar"
            small
            class="ml-auto"
            :href="route('certificate.download', { event: event.id, user: user.id })"
            target="_blank"
          />
          <BaseButton
            color="gray"
            :icon="mdiEye"
            label="Previsualizar"
            small
            @click="openPreview(user.id)"
          />
        </div>
      </div>

      <BaseButton
        class="mt-4"
        color="success"
        label="Generar certificados"
        @click="submit"
      />
    </CardBox>

    <!-- Modal de previsualización -->
    <div
      v-if="previewUserId"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
      @click.self="closePreview"
    >
      <div class="bg-white w-[90vw] max-w-5xl h-[90vh] p-4 rounded shadow-lg overflow-hidden relative">
        <button @click="closePreview" class="absolute top-2 right-4 text-gray-700 hover:text-red-500 text-xl">×</button>
        <iframe
          v-if="previewUrl"
          :src="previewUrl"
          class="w-full h-full border rounded"
        ></iframe>
      </div>
    </div>
  </LayoutMain>
</template>
