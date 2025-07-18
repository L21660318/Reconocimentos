<script setup>
import CardBox from "@/Components/CardBox.vue";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import {
    mdiContentSave,
    mdiClose,
    mdiPlus,
    mdiTrashCan
} from "@mdi/js";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { Link, Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import { computed, watch, ref, provide } from "vue";
import CardBoxComponentEmpty from "@/Components/CardBoxComponentEmpty.vue";
import JetInputError from '@/Components/InputError.vue';
import { Tabs, Tab } from "flowbite-vue";
import Card from './Partials/Card.vue'
import InputError from "@/Components/InputError.vue";
import Select from '@/Components/Select.vue';
import axios from "axios";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    name: 'Create',
    title: {
        type: String,
        required: true
    },
    routeName: {
        type: String,
        required: true
    },
    areas: { type: Object, default: {}, required: true },
    calls: { type: Object, default: {}, required: true },
    callId: { default: null }
});

const areasData = props.areas.filter(area => area.parent_id === null) ?? []
const subareasData = ref([])
const activeTab = ref('call')
const results = ref(null);

const form = useForm({
    title: null,
    type: 'Artículo (6 hojas)',
    abstract: null,
    key_works: null,
    knowledge_area_id: null,
    article_status_id: 1, // de la tabla
    call_id: props.callId,
    file: null,
    authors: [],

    selectedArea: null
});

const saveForm = () => {
    form.post(route(`${props.routeName}store`), {
        onError: (errors) => {
            if (errors.call_id) {
                activeTab.value = 'call'
            } else {
                activeTab.value = 'general'
            }
        }
    });
};

const filterInstitutions = (search) => {
    axios.get(route(`institution.filter`, search))
        .then((response) => {
            results.value = response.data;
            console.log(results);
        }).catch(function (error) {
            if (error.response) {
                if (error.response.status == 500) {
                    Swal.fire({
                        title: "ERRO!",
                        text: "Error",
                        icon: "info",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ok!",
                    });
                }
            }
        });
};

const handleFileInput = (event) => {
    form.file = event.target.files[0];
};

const getFileURL = computed(() => {
    if (form.file !== null) {
        return URL.createObjectURL(form.file)
    }
});

const prefixes = [
    { id: 'Doctor(a)', name: 'Doctor(a)' },
    { id: 'Maestro(a)', name: 'Maestro(a)' },
    { id: 'Ingeniero(a)', name: 'Ingeniero(a)' },
    { id: 'Licenciado(a)', name: 'Licenciado(a)' },
    { id: 'Profesor(a)', name: 'Profesor(a)' },
];

const addAuthor = () => {
    form.authors.push({
        prefix: null,
        name: null,
        surname: null,
        institution_id: null,
        email: null,
    });
};

const removeAuthor = (index) => {
    if (form.authors.length > 0) {
        form.authors.splice(index, 1);
    }
};

const getErrorMessage = (index, field) => {
    const errorKey = `authors.${index}.${field}`;
    return form.errors[errorKey] ?? null;
}

const filterAreas = () => {
    form.knowledge_area_id = null
    subareasData.value = props.areas.filter(area => area.parent_id === form.selectedArea)
}

const getItem = (id) => {
    if (id) {
        return results.value?.find(result => result.id === id);
    }
    return null;
}

watch(() => form.selectedArea, filterAreas);

provide('calls', props.calls)
provide('form', form)

</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main>
            <Link :href="route(`${routeName}index`)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x"
                viewBox="0 0 16 16">
                <path
                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
            </svg>
            </Link>
        </SectionTitleLineWithButton>

        <CardBox isForm @submit.prevent="saveForm">
            <Tabs v-model="activeTab" variant="underline" class="p-5">
                <Tab name="call" title="Convocatoria" :disabled="false">
                    <div v-if="calls.length > 0"
                        class="h-[500px] py-5 overflow-auto justify-center gap-4 grid sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                        <div v-for="call in calls" :key="call.id" class="mb-2">
                            <Card :call="call" />
                        </div>
                    </div>
                    <CardBoxComponentEmpty v-else />
                    <InputError :message="form.errors.call_id" />
                </Tab>

                <Tab name="general" title="Datos generales" :disabled="false">
                    <FormField required label="Titulo:" :error="form.errors.title">
                        <FormControl height="h-12" v-model="form.title" placeholder="Titulo" />
                    </FormField>

                    <FormField required label="Estado:" :error="form.errors.state_id">
                        <div class="flex items-center ps-4 border border-gray-400 rounded dark:border-gray-700 h-12">
                            <input id="bordered-radio-1" type="radio" value="Artículo (6 hojas)" v-model="form.type"
                                name="bordered-radio"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-800 dark:border-gray-600">
                            <label for="bordered-radio-1"
                                class="w-full ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                Artículo normal
                                <p class="text-xs font-normal text-gray-500 dark:text-gray-300">
                                    6 hojas
                                </p>
                            </label>
                        </div>
                        <div class="flex items-center ps-4 border border-gray-400 rounded dark:border-gray-700 h-12">
                            <input id="bordered-radio-2" type="radio" value="Artículo corto - Poster (2 hojas)"
                                v-model="form.type" name="bordered-radio"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-800 dark:border-gray-600">
                            <label for="bordered-radio-2"
                                class="w-full ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                Artículo corto
                                <p class="text-xs font-normal text-gray-500 dark:text-gray-300">
                                    Poster (2 hojas)
                                </p>
                            </label>
                        </div>
                    </FormField>

                    <FormField required label="Resumen:" :error="form.errors.abstract">
                        <FormControl type="textarea" height="h-56" v-model="form.abstract" placeholder="Resumen" />
                    </FormField>
                    <div class="md:flex md:space-x-4 my-5">
                        <div class="md:w-1/2 max-lg:mb-5">
                            <FormField label="Area de conocimiento:">
                                <FormControl v-model="form.selectedArea" :options="areasData" />
                            </FormField>
                        </div>
                        <div class="md:w-1/2">
                            <FormField required label="SubArea:" :error="form.errors.knowledge_area_id">
                                <FormControl v-model="form.knowledge_area_id" :options="subareasData" />
                            </FormField>
                        </div>
                    </div>

                    <FormField required label="Palabras clave:" :error="form.errors.key_works">
                        <FormControl v-model="form.key_works" placeholder="Palabras clave" />
                    </FormField>

                    <div class="my-6">
                        <div class="justify-between flex mb-6">
                            <label class="font-bold">Lista de autores</label>
                            <BaseButton :icon="mdiPlus" @click="addAuthor" label="Agregar autor" color="info" />
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <table v-if="form.authors.length > 0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Prefijo</th>
                                        <th>Nombre(s)</th>
                                        <th>Apellido(s)</th>
                                        <th>Institución</th>
                                        <th>Email</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(author, index) in form.authors" :key="index" class="z-0">
                                        <td data-label="No">
                                            {{ index + 1 }}
                                        </td>
                                        <td data-label="Prefijo">
                                            <FormControl v-model="author.prefix" :options="prefixes" height="h-8"
                                                textClass="tex-xs" />
                                            <JetInputError :message="getErrorMessage(index, 'prefix')" />
                                        </td>
                                        <td data-label="Nombre(s)">
                                            <FormControl v-model="author.name" placeholder="Nombre" height="h-8"
                                                textClass="tex-sm" />
                                            <JetInputError :message="getErrorMessage(index, 'name')" />
                                        </td>
                                        <td data-label="Apellido(s)">
                                            <FormControl v-model="author.surname" placeholder="Apellido" height="h-8"
                                                textClass="tex-sm" />
                                            <JetInputError :message="getErrorMessage(index, 'surname')" />
                                        </td>
                                        <td data-label="Institución">
                                            <Select placeholder="Busca una institución" height="h-8"
                                                v-model="author.institution_id" required :options="results"
                                                @search="filterInstitutions"
                                                :itemSelected="getItem(author.institution_id)"
                                                dropdownClass="p-2 relative w-full z-50 bg-white dark:bg-gray-800 dark:text-white border border-gray-300 mt-1 max-h-32 overflow-hidden overflow-y-scroll rounded-md shadow-md" />
                                            <JetInputError :message="getErrorMessage(index, 'institution_id')" />
                                        </td>
                                        <td data-label="Email">
                                            <FormControl v-model="author.email" placeholder="Email" height="h-8"
                                                textClass="text-sm" />
                                            <JetInputError :message="getErrorMessage(index, 'email')" />
                                        </td>
                                        <td data-label="Acciones">
                                            <BaseButtons>
                                                <BaseButton :icon="mdiTrashCan" small @click="removeAuthor(index)"
                                                    color="danger" />
                                            </BaseButtons>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <CardBoxComponentEmpty v-else />
                        </div>
                    </div>

                    <FormField label="Cargar archivo PDF:" required help="Solo archivos PDF (MAX 100M)"
                        :error="form.errors.file">
                        <div
                            class="bg-slate-100 border-4 border-dashed border-gray-400 flex flex-col items-center justify-center rounded-lg shadow-lg p-6 md:p-10 mb-4 dark:bg-gray-800 dark:border-gray-600">
                            <div class="w-auto mb-6 text-sm md:text-base font-medium text-gray-700 dark:text-gray-300">
                                <FormControl @input="handleFileInput" height="h-10.5" type="file" class="w-full" />
                                <p class="font-semibold mt-4">
                                    Nombre del archivo: {{ form.file?.name }}
                                </p>
                                <p class="font-semibold">
                                    Tamaño: {{ (form.file?.size / 1000).toFixed(2) }} KB
                                </p>
                            </div>
                            <div class="w-full flex justify-center mt-8 mb-4">
                                <template v-if="form.file">
                                    <iframe v-if="form.file.type === 'application/pdf'"
                                        class="w-full h-96 border rounded-lg shadow-md" :src="getFileURL" />
                                    <span v-else class="text-slate-600 text-2xl">Formato no disponible!</span>

                                </template>
                            </div>
                        </div>
                    </FormField>
                </Tab>
            </Tabs>
            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
                    <BaseButton @click="saveForm" :icon="mdiContentSave" color="success" label="Guardar" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
