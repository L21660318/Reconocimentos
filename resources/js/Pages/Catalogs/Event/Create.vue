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

const props = defineProps({
    title: String,
    routeName: String,
});

const form = useForm({
    nombre: '',
    tipo: '',
    fecha_inicio: '',
    fecha_fin: '',
});

const saveForm = () => {
    form.post(route(`${props.routeName}store`));
};
</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiPlus" :title="title" main />
        <CardBox isForm @submit.prevent="saveForm">
            <FormField label="Nombre" :error="form.errors.nombre" required>
                <FormControl v-model="form.nombre" />
            </FormField>
            <FormField label="Tipo" :error="form.errors.tipo">
                <FormControl v-model="form.tipo" />
            </FormField>
            <FormField label="Fecha de inicio" :error="form.errors.fecha_inicio" required>
                <FormControl type="date" v-model="form.fecha_inicio" />
            </FormField>
            <FormField label="Fecha de fin" :error="form.errors.fecha_fin" required>
                <FormControl type="date" v-model="form.fecha_fin" />
            </FormField>

            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
                    <BaseButton :icon="mdiContentSave" color="success" label="Guardar" type="submit" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
