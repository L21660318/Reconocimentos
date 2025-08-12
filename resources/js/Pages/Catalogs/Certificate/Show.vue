<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue';
import SectionTitleLineWithButton from '@/Components/SectionTitleLineWithButton.vue';
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import FormField from '@/Components/FormField.vue';
import { router } from '@inertiajs/vue3';
import { mdiAccountSchool, mdiSelectAll, mdiCheckboxBlankOutline, mdiEye, mdiDownload, mdiClose, mdiCheckCircle } from '@mdi/js';
import { ref, computed, watch } from 'vue';

const props = defineProps({
  event: Object,
  title: String
});

const tipo = ref('Participación');
const selectedUsers = ref([]);
const search = ref('');
const previewUserId = ref(null);
const isLoading = ref(false);
const previewLoading = ref(false);
const showDownloadModal = ref(false);
const downloadUrl = ref('');
const generatedCount = ref(0);

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

const downloadCertificate = async (userId) => {
  window.open(route('certificates.download', {
    event: props.event.id,
    user: userId,
    tipo: tipo.value
  }), '_blank');
};

const generateCertificates = async () => {
  if (selectedUsers.value.length === 0) {
    alert('Selecciona al menos un usuario.');
    return;
  }

  isLoading.value = true;
  generatedCount.value = selectedUsers.value.length;

  try {
    // Para un solo certificado
    if (selectedUsers.value.length === 1) {
      await downloadCertificate(selectedUsers.value[0]);
      return;
    }

    // Para múltiples certificados
    const response = await router.post(route('certificates.generate.batch', { event: props.event.id }), {
      user_ids: selectedUsers.value,
      tipo: tipo.value
    });

    if (response?.data?.download_url) {
      downloadUrl.value = response.data.download_url;
      showDownloadModal.value = true;
    } else {
      showDownloadModal.value = true; // Mostrar modal incluso si no hay URL (por si falla la descarga)
    }
  } catch (error) {
    console.error('Error al generar certificados:', error);
    alert('Ocurrió un error al generar los certificados');
  } finally {
    isLoading.value = false;
  }
};

const startDownload = () => {
  if (downloadUrl.value) {
    window.location.href = downloadUrl.value;
  }
  showDownloadModal.value = false;
};

const openPreview = async (userId) => {
  previewLoading.value = true;
  previewUserId.value = userId;
  
  // Forzar recarga del iframe
  await new Promise(resolve => setTimeout(resolve, 50));
  
  const iframe = document.querySelector('.preview-iframe');
  if (iframe) {
    iframe.src = previewUrl.value;
    iframe.onload = () => {
      previewLoading.value = false;
    };
  }
};

const previewUrl = computed(() => {
  if (!previewUserId.value) return null;
  
  return route('certificates.preview', { 
    event: props.event.id,
    user: previewUserId.value, 
    tipo: tipo.value,
    timestamp: Date.now()
  });
});

const closePreview = () => {
  previewUserId.value = null;
  previewLoading.value = false;
};

// Detectar tecla ESC para cerrar el modal
const handleKeyDown = (e) => {
  if (e.key === 'Escape') {
    closePreview();
  }
};

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
        <BaseButton 
          :icon="mdiSelectAll" 
          color="info" 
          label="Marcar todos" 
          @click="checkAll" 
        />
        <BaseButton 
          :icon="mdiCheckboxBlankOutline" 
          color="warning" 
          label="Desmarcar todos" 
          @click="uncheckAll" 
        />
      </div>

      <div class="grid gap-2 max-h-[60vh] overflow-y-auto">
        <div
          v-for="user in uniqueUsers"
          :key="user.id"
          class="flex items-center gap-4 p-2 hover:bg-gray-50"
        >
          <input
            type="checkbox"
            :value="user.id"
            v-model="selectedUsers"
            class="h-4 w-4 text-blue-600 rounded"
          />
          <div class="flex-1 min-w-0">
            <p class="font-medium truncate">{{ user.name }}</p>
            <p class="text-sm text-gray-500 truncate">
              {{ user.email }}
            </p>
            <p class="text-xs text-gray-400">
              Área: {{ user.knowledge_area?.name ?? 'No asignada' }}
            </p>
          </div>

          <div class="flex gap-2">
            <BaseButton
              color="info"
              :icon="mdiDownload"
              label="Descargar"
              small
              @click="downloadCertificate(user.id)"
            />
            <BaseButton
              color="gray"
              :icon="mdiEye"
              label="Vista previa"
              small
              @click="openPreview(user.id)"
            />
          </div>
        </div>
      </div>

      <BaseButton
        class="mt-4 w-full"
        color="success"
        :label="isLoading ? 'Generando...' : `Generar ${selectedUsers.length} certificado(s)`"
        :disabled="isLoading || selectedUsers.length === 0"
        @click="generateCertificates"
      />
    </CardBox>

    <!-- Modal de previsualización -->
    <div
      v-if="previewUserId"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
      @click.self="closePreview"
    >
      <div class="bg-white w-[90vw] max-w-5xl h-[90vh] p-4 rounded shadow-lg overflow-hidden relative">
        <button 
          @click="closePreview" 
          class="absolute top-2 right-2 p-1 text-gray-700 hover:text-red-500 rounded-full hover:bg-gray-100 transition-colors"
          :disabled="previewLoading"
        >
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
        
        <div v-if="previewLoading" class="flex items-center justify-center h-full">
          <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <p class="text-gray-600">Generando vista previa...</p>
          </div>
        </div>
        
        <iframe
          v-show="!previewLoading"
          :src="previewUrl"
          class="w-full h-full border rounded preview-iframe"
          frameborder="0"
        ></iframe>
      </div>
    </div>

    <!-- Modal de descarga exitosa -->
    <div
      v-if="showDownloadModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
      @click.self="showDownloadModal = false"
    >
      <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-xl">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
          <h3 class="mt-3 text-lg font-medium text-gray-900">
            Generación completada
          </h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500">
              Se generaron exitosamente <span class="font-semibold">{{ generatedCount }} certificados</span>.
            </p>
            <p class="mt-2 text-sm text-gray-500">
              El archivo ZIP contiene todos los certificados en formato PDF listos para imprimir.
            </p>
          </div>
          <div class="mt-6">
            <BaseButton
              color="success"
              class="w-full justify-center"
              :icon="mdiDownload"
              label="Descargar archivo ZIP"
              @click="startDownload"
            />
            <BaseButton
              color="gray"
              class="w-full justify-center mt-3"
              label="Cerrar"
              @click="showDownloadModal = false"
            />
          </div>
        </div>
      </div>
    </div>
  </LayoutMain>
</template>