<script setup>
import { useForm } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import { mdiContentSave, mdiClose, mdiPlus } from "@mdi/js";
import HeadLogo from "@/Components/HeadLogo.vue";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import { ref } from "vue";

const props = defineProps({
  title: String,
  routeName: String,
  institutions: Array,
});

const form = useForm({
  nombre: '',
  tipo: '',
  fecha_inicio: '',
  fecha_fin: '',
  institution_id: '',
  imagen: null,
  archivo_pdf: null,
});

const imagePreview = ref(null);
const pdfPreview = ref(null);

// 🖼 Validación y previsualización de imagen
const onImageChange = (e) => {
  const file = e.target.files[0];
  const input = e.target;

  if (!file) return;

  if (!file.type.startsWith("image/")) {
    alert("El archivo no es una imagen válida (jpeg, jpg o png).");
    input.value = '';
    form.imagen = null;
    imagePreview.value = null;
    return;
  }

  if (file.size > 1 * 1024 * 1024) {
    alert("La imagen supera el tamaño máximo permitido de 1MB.");
    input.value = '';
    form.imagen = null;
    imagePreview.value = null;
    return;
  }

  form.imagen = file;

  const reader = new FileReader();
  reader.onload = () => {
    imagePreview.value = reader.result;
  };
  reader.readAsDataURL(file);
};

// 📄 Validación y previsualización de PDF
const onPdfChange = (e) => {
  const file = e.target.files[0];
  const input = e.target;

  if (!file) return;

  if (file.type !== "application/pdf") {
    alert("El archivo no es un PDF válido.");
    input.value = '';
    form.archivo_pdf = null;
    pdfPreview.value = null;
    return;
  }

  if (file.size > 10 * 1024 * 1024) {
    alert("El archivo PDF supera el tamaño máximo permitido de 10MB.");
    input.value = '';
    form.archivo_pdf = null;
    pdfPreview.value = null;
    return;
  }

  form.archivo_pdf = file;
  const url = URL.createObjectURL(file);
  pdfPreview.value = url;
};

const saveForm = () => {
  form.post(route(`${props.routeName}store`), {
    forceFormData: true,
  });
};
</script>

<template>
  <HeadLogo :title="title" />
  <LayoutMain>
    <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main />

    <CardBox isForm @submit.prevent="saveForm">
      <!-- NOMBRE -->
      <FormField label="Nombre" :error="form.errors.nombre" required>
        <FormControl v-model="form.nombre" />
      </FormField>

      <!-- TIPO -->
      <FormField label="Tipo" :error="form.errors.tipo">
        <FormControl v-model="form.tipo" />
      </FormField>

      <!-- FECHA INICIO -->
      <FormField label="Fecha de inicio" :error="form.errors.fecha_inicio" required>
        <FormControl type="date" v-model="form.fecha_inicio" />
      </FormField>

      <!-- FECHA FIN -->
      <FormField label="Fecha de fin" :error="form.errors.fecha_fin" required>
        <FormControl type="date" v-model="form.fecha_fin" />
      </FormField>

      <!-- INSTITUCIÓN -->
      <FormField label="Institución" :error="form.errors.institution_id">
        <v-select
          v-model="form.institution_id"
          :options="institutions"
          label="name"
          :reduce="inst => inst.id"
          placeholder="Buscar institución..."
          :searchable="true"
          :clearable="true"
        />
      </FormField>

      <!-- CARGAR PDF -->
      <FormField label="Cargar archivo PDF" :error="form.errors.archivo_pdf" required>
        <div class="border-2 border-dashed border-gray-500 p-4 bg-gray-900 rounded-lg text-white">
          <input
            type="file"
            accept="application/pdf"
            @change="onPdfChange"
            class="w-full text-white file:bg-gray-800 file:border-none file:px-4 file:py-2 file:mr-4 file:text-sm file:text-white file:rounded hover:file:bg-gray-700"
          />

          <div v-if="pdfPreview" class="mt-4">
            <iframe
              :src="pdfPreview"
              class="w-full h-[500px] rounded border border-gray-600"
            ></iframe>
          </div>

          <p class="text-sm text-gray-400 mt-2">
            Solo archivos PDF (MAX 10MB) <span title="Tamaño máximo permitido">❔</span>
          </p>
        </div>
      </FormField>

      <!-- CARGAR IMAGEN -->
      <FormField label="Cargar imagen" :error="form.errors.imagen" required>
        <div class="border-2 border-dashed border-gray-500 p-4 bg-gray-900 rounded-lg text-white">
          <input
            type="file"
            accept="image/*"
            @change="onImageChange"
            class="w-full text-white file:bg-gray-800 file:border-none file:px-4 file:py-2 file:mr-4 file:text-sm file:text-white file:rounded hover:file:bg-gray-700"
          />

          <div v-if="imagePreview" class="mt-4">
            <img
              :src="imagePreview"
              class="max-h-[400px] rounded border border-gray-600 mx-auto"
              alt="Vista previa de la imagen"
            />
          </div>

          <p class="text-sm text-gray-400 mt-2">
            Solo archivos jpeg, jpg y png (MAX 1MB) <span title="Tamaño máximo permitido">❔</span>
          </p>
        </div>
      </FormField>

      <!-- BOTONES -->
      <template #footer>
        <BaseButtons>
          <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
          <BaseButton :icon="mdiContentSave" color="success" label="Guardar" type="submit" />
        </BaseButtons>
      </template>
    </CardBox>
  </LayoutMain>
</template>
