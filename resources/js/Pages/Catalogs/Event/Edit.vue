<script setup>
import { ref } from 'vue';
import { useForm } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import { mdiContentSave, mdiClose, mdiPencil, mdiTrashCan } from "@mdi/js";
import Swal from 'sweetalert2';
import HeadLogo from "@/Components/HeadLogo.vue";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

const props = defineProps({
    title: String,
    routeName: String,
    event: Object,
    institutions: Array,
});

const form = useForm({
    id: props.event?.id || '',
    nombre: props.event?.nombre || '',
    tipo: props.event?.tipo || '',
    descripcion: props.event?.descripcion || '',
    fecha_inicio: props.event?.fecha_inicio || '',
    fecha_fin: props.event?.fecha_fin || '',
    institution_id: props.event?.institution_id || null,
    imagen: null,
    archivo_pdf: null,
});

const imagePreview = ref(props.event?.imagen ? `/storage/${props.event.imagen}` : null);
const pdfPreview = ref(props.event?.archivo_pdf ? `/storage/${props.event.archivo_pdf}` : null);

// Manejo de cambios en la imagen
const onImageChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (!file.type.startsWith("image/")) {
        Swal.fire({
            icon: 'error',
            title: 'Formato incorrecto',
            text: 'El archivo debe ser una imagen (JPEG, JPG o PNG)',
            timer: 2000
        });
        e.target.value = '';
        return;
    }

    if (file.size > 1 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'La imagen no debe superar 1MB',
            timer: 2000
        });
        e.target.value = '';
        return;
    }

    form.imagen = file;
    imagePreview.value = URL.createObjectURL(file);
};

// Manejo de cambios en el PDF
const onPdfChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    if (file.type !== "application/pdf") {
        Swal.fire({
            icon: 'error',
            title: 'Formato incorrecto',
            text: 'El archivo debe ser un PDF',
            timer: 2000
        });
        e.target.value = '';
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'Archivo muy grande',
            text: 'El PDF no debe superar 10MB',
            timer: 2000
        });
        e.target.value = '';
        return;
    }

    form.archivo_pdf = file;
    pdfPreview.value = URL.createObjectURL(file);
};

// Guardar el formulario
const saveForm = () => {
    form.transform((data) => ({
        ...data,
        _method: 'PUT'
    })).post(route(`${props.routeName}update`, form.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Evento actualizado',
                showConfirmButton: false,
                timer: 1500,
                background: '#1f2937',
                color: '#fff'
            });
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: Object.values(errors).join(' '),
                background: '#1f2937',
                color: '#fff',
                confirmButtonColor: '#3b82f6'
            });
        }
    });
};

// Eliminar el evento
const deleteForm = () => {
    Swal.fire({
        title: "¿Eliminar evento?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: '#d33',
        background: '#1f2937',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            form.delete(route(`${props.routeName}destroy`, form.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El evento ha sido eliminado',
                        icon: 'success',
                        timer: 1500,
                        background: '#1f2937',
                        color: '#fff'
                    });
                },
                onError: () => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el evento',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            });
        }
    });
};
</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiPencil" :title="title" main />

        <CardBox isForm @submit.prevent="saveForm">
            <!-- Nombre del Evento -->
            <FormField label="Nombre" :error="form.errors.nombre" required>
                <FormControl v-model="form.nombre" placeholder="Nombre del evento" />
            </FormField>

            <!-- Tipo de Evento -->
            <FormField label="Tipo" :error="form.errors.tipo">
                <FormControl v-model="form.tipo" placeholder="Tipo de evento" />
            </FormField>

            <!-- Descripción -->
            <FormField label="Descripción" :error="form.errors.descripcion">
                <FormControl 
                    v-model="form.descripcion" 
                    type="textarea" 
                    placeholder="Descripción detallada del evento..."
                    rows="5"
                />
            </FormField>

            <!-- Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormField label="Fecha de inicio" :error="form.errors.fecha_inicio" required>
                    <FormControl type="date" v-model="form.fecha_inicio" />
                </FormField>
                <FormField label="Fecha de fin" :error="form.errors.fecha_fin" required>
                    <FormControl type="date" v-model="form.fecha_fin" :min="form.fecha_inicio" />
                </FormField>
            </div>

            <!-- Institución -->
            <FormField label="Institución" :error="form.errors.institution_id">
                <v-select
                    v-model="form.institution_id"
                    :options="institutions"
                    label="name"
                    :reduce="inst => inst.id"
                    placeholder="Seleccione una institución"
                    :clearable="true"
                    class="bg-gray-800 rounded"
                />
            </FormField>

            <!-- Imagen -->
            <FormField label="Imagen del Evento" :error="form.errors.imagen">
                <div class="space-y-2">
                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 bg-gray-900">
                        <input 
                            type="file" 
                            @change="onImageChange" 
                            accept="image/*"
                            class="block w-full text-sm text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700 cursor-pointer"
                        />
                    </div>
                    <div v-if="imagePreview" class="mt-2">
                        <p class="text-sm text-gray-400 mb-1">Vista previa:</p>
                        <img :src="imagePreview" class="max-h-60 rounded-md border border-gray-700" alt="Preview de imagen">
                        <p class="text-xs text-gray-500 mt-1">La imagen se guardará en: eventos/imagenes/</p>
                    </div>
                    <p class="text-xs text-gray-400">Formatos: JPEG, JPG, PNG (Max 1MB)</p>
                </div>
            </FormField>

            <!-- PDF -->
            <FormField label="Documento PDF" :error="form.errors.archivo_pdf">
                <div class="space-y-2">
                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 bg-gray-900">
                        <input 
                            type="file" 
                            @change="onPdfChange" 
                            accept="application/pdf"
                            class="block w-full text-sm text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-600 file:text-white
                                hover:file:bg-blue-700 cursor-pointer"
                        />
                    </div>
                    <div v-if="pdfPreview" class="mt-2">
                        <p class="text-sm text-gray-400 mb-1">Documento actual:</p>
                        <a :href="pdfPreview" target="_blank" class="inline-flex items-center text-blue-400 hover:text-blue-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Ver documento PDF
                        </a>
                        <p class="text-xs text-gray-500 mt-1">El PDF se guardará en: eventos/archivos/</p>
                    </div>
                    <p class="text-xs text-gray-400">Formato: PDF (Max 10MB)</p>
                </div>
            </FormField>

            <!-- Botones -->
            <template #footer>
                <BaseButtons>
                    <BaseButton 
                        :routeName="`${routeName}index`" 
                        :icon="mdiClose" 
                        color="white" 
                        label="Cancelar" 
                        :disabled="form.processing"
                    />
                    <BaseButton 
                        :icon="mdiContentSave" 
                        color="success" 
                        label="Guardar Cambios" 
                        type="submit" 
                        :disabled="form.processing"
                        class="hover:bg-green-700"
                    />
                    <BaseButton 
                        :icon="mdiTrashCan" 
                        color="danger" 
                        label="Eliminar Evento" 
                        @click="deleteForm" 
                        :disabled="form.processing"
                        class="hover:bg-red-700"
                    />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>