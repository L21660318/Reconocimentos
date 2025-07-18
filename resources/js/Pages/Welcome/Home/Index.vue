<script setup>
import HeadLogo from '@/Components/HeadLogo.vue';
import Events from './Partials/Events/Events.vue';
import HeroSection from './Partials/HeroSection.vue';
import LayoutWelcome from '@/Layouts/LayoutWelcome.vue';
import { Head, Link } from '@inertiajs/vue3';
const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    calls:{
        type: Object,
        required: true
    },
    events:{
        type: Object,
        required: true
    },
    search: { type: String, required: true },
    title: {
        type: String,
        required: true
    }
    
});

</script>

<template>
    <HeadLogo :title="title" />
    <LayoutWelcome>
        <HeroSection>
            <template #title>
                Fer Preciosa
            </template>
            <template #description>
                Divulgación de la tecnología y ciencia aplicada en la solución de problemas como soberanía en salud,
                soberanía alimentaria, soberanía energética, desarrollos socioambientales y desarrollo
                urbano-industrial.
            </template>
        </HeroSection>
        
        <Events :events="events" :search="search" />
    </LayoutWelcome>
</template>
