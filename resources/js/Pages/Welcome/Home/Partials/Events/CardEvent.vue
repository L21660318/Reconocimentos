<script setup>
import BaseButton from '@/Components/BaseButton.vue';
import { mdiArrowRight } from '@mdi/js';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div
        class="pb-4 rounded-md border shadow-sm w-full max-w-sm group transition-all duration-200 hover:translate-y-2.5 hover:shadow-md  dark:border-gray-800 dark:bg-gray-800">

        <img :src="event.imagen ? '/storage/' + event.imagen : '/img/login-image.jpg'"
            alt="imagen evento" class="h-60 w-full rounded-t object-cover">

        <div class="px-4">
            <div class="mt-3 text-left space-y-2 ">
                <span class="text-sm font-light text-gray-800 dark:text-gray-400">{{ event.fecha_inicio }}</span>
                <h4 class="line-clamp-2 h-14 mt-2 text-lg font-semibold group-hover:text-blue-500 text-gray-800 dark:text-white">
                    {{ event.nombre }}
                </h4>
            </div>
            <div class="justify-end flex mt-4">
                <BaseButton small :icon="mdiArrowRight" @click="$emit('openModal', event)" color="info"
                    label="Conocer más" />
            </div>
        </div>
    </div>
</template>
