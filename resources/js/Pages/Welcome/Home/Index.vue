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
                Plataforma de Reconocimientos del CENIDET
            </template>
            <template #description>
                Regístrate, participa en eventos académicos y obtén reconocimientos digitales
                validados por el CENIDET. Todo en un solo lugar, de forma segura y accesible.
            </template>
        </HeroSection>

        
        <Events :events="events" :search="search" />
    </LayoutWelcome>
</template>
