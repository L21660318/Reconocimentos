<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { mdiContentSave, mdiClose } from "@mdi/js";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    title: String,
    event: Object,
    pivot: Object,
});

const form = useForm({
    estatus: props.pivot?.estatus || 'pendiente',
    comentario: props.pivot?.comentario || '',
});

const saveForm = () => {
    form.put(route('event-review.update', props.event.id));
};
</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiContentSave" :title="title" main>
            <Link :href="route('event-review.index')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x"
                    viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </Link>
        </SectionTitleLineWithButton>

        <CardBox isForm @submit.prevent="saveForm">
            <FormField label="Nombre del evento">
                <FormControl v-model="event.nombre" disabled />
            </FormField>

            <FormField label="Estatus" :error="form.errors.estatus" required>
                <select v-model="form.estatus" class="input">
                    <option value="pendiente">Pendiente</option>
                    <option value="aceptado">Aceptado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </FormField>

            <FormField label="Comentario" :error="form.errors.comentario">
                <FormControl height="h-32" type="textarea" v-model="form.comentario" placeholder="Comentarios del revisor..." />
            </FormField>

            <template #footer>
                <BaseButtons>
                    <BaseButton :href="route('event-review.index')" :icon="mdiClose" color="white" label="Cancelar" />
                    <BaseButton :icon="mdiContentSave" color="success" label="Guardar" @click="saveForm" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
