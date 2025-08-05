<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { mdiAccount, mdiContentSave, mdiClose, mdiSelectAll, mdiCheckboxBlankOutline, mdiFileExcel, mdiUpload } from "@mdi/js";
import HeadLogo from "@/Components/HeadLogo.vue";
import { ref, computed } from "vue";

const props = defineProps({
  title: String,
  routeName: String,
  event: Object,
  users: Array,
  assigned: Array,
});

const form = useForm({
  users: props.assigned,
  excelFile: null,
});

const search = ref("");
const showImportModal = ref(false);

const filteredUsers = computed(() => {
  const term = search.value.toLowerCase();
  return props.users.filter(
    (user) =>
      user.name.toLowerCase().includes(term) || user.email.toLowerCase().includes(term)
  );
});

const saveForm = () => {
  // Asumiendo que props.routeName es "events."
  form.post(route(`${props.routeName}storeUsers`, { event: props.event.id }));
};

const importUsers = () => {
  // Cambia esto:
  form.post(route('events.importUsers', { event: props.event.id }), {
    onSuccess: () => {
      showImportModal.value = false;
      form.reset('excelFile');
    },
    forceFormData: true,
  });
};

const checkAll = () => {
  const idsToAdd = filteredUsers.value.map((u) => u.id);
  form.users = Array.from(new Set([...form.users, ...idsToAdd]));
};

const uncheckAll = () => {
  form.users = form.users.filter((id) => !filteredUsers.value.some((u) => u.id === id));
};
</script>

<template>
  <HeadLogo :title="title" />
  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiAccount" :title="title" main>
      <div class="flex items-center gap-2">
        <BaseButton 
          :icon="mdiFileExcel" 
          color="info" 
          label="Importar Excel" 
          @click="showImportModal = true"
          small
        />

        <BaseButton 
          :icon="mdiDownload"
          color="info" 
          label="Descargar plantilla"
          :href="route('users.downloadTemplate')"
          download
          small
          outline
        />
        <Link :href="route(`${routeName}index`)">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
          </svg>
        </Link>
      </div>
    </SectionTitleLineWithButton>

    <!-- Modal para importar Excel -->
    <div v-if="showImportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <CardBox class="w-full max-w-md">
        <h3 class="text-lg font-medium mb-4">Importar usuarios desde Excel</h3>
        
        <div class="mb-4">
          <label class="block text-sm font-medium mb-2">Archivo Excel</label>
          <input 
            type="file" 
            @input="form.excelFile = $event.target.files[0]"
            accept=".xlsx,.xls,.csv"
            class="block w-full text-sm text-gray-500
              file:mr-4 file:py-2 file:px-4
              file:rounded-md file:border-0
              file:text-sm file:font-semibold
              file:bg-blue-50 file:text-blue-700
              hover:file:bg-blue-100"
          />
          <p class="mt-1 text-xs text-gray-500">Formato soportado: .xlsx, .xls, .csv</p>
        </div>

        <div class="flex justify-end gap-2">
          <BaseButton 
            label="Cancelar" 
            color="white" 
            :icon="mdiClose"
            @click="showImportModal = false" 
          />
          <BaseButton 
            label="Importar" 
            color="success" 
            :icon="mdiUpload"
            @click="importUsers" 
            :disabled="!form.excelFile"
          />
        </div>
      </CardBox>
    </div>

    <CardBox isForm @submit.prevent="saveForm">
      <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <input
          v-model="search"
          type="text"
          placeholder="Buscar por nombre o correo..."
          class="w-full sm:w-1/2 rounded-md border-gray-300 text-sm shadow-sm focus:ring focus:ring-blue-200"
        />

        <div class="flex gap-2">
          <BaseButton :icon="mdiSelectAll" color="info" label="Marcar todo" @click="checkAll" />
          <BaseButton :icon="mdiCheckboxBlankOutline" color="warning" label="Desmarcar todo" @click="uncheckAll" />
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 max-h-[60vh] overflow-y-auto">
        <div v-for="user in filteredUsers" :key="user.id" class="flex items-center space-x-2">
          <input
            type="checkbox"
            :id="`user-${user.id}`"
            :value="user.id"
            v-model="form.users"
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200"
          />
          <label :for="`user-${user.id}`" class="text-sm dark:text-white">
            {{ user.name }} ({{ user.email }})
          </label>
        </div>
      </div>

      <template #footer>
        <BaseButtons>
          <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
          <BaseButton :icon="mdiContentSave" color="success" type="submit" label="Guardar" @click="saveForm" />
        </BaseButtons>
      </template>
    </CardBox>
  </LayoutMain>
</template>