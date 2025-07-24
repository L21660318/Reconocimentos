<script setup>
import BaseButton from '@/Components/BaseButton.vue';
import { mdiArrowRight } from '@mdi/js';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    event: {
        type: Object,
        required: true
    }
});

const showForm = ref(false);
const email = ref('');
const loading = ref(false);
const message = ref('');
const error = ref('');

const inscribirse = () => {
    if (!email.value) {
        error.value = 'Por favor ingresa un correo electrónico.';
        return;
    }

    loading.value = true;
    message.value = '';
    error.value = '';

    router.post(route('event.register', props.event.id), { email: email.value }, {
        onSuccess: () => {
            message.value = 'Solicitud de inscripción enviada correctamente.';
            email.value = '';
            showForm.value = false;
        },
        onError: (errors) => {
            error.value = errors.email || 'Ocurrió un error. Verifica tu correo.';
        },
        onFinish: () => {
            loading.value = false;
        }
    });
};
</script>

<template>
    <div
        class="pb-4 rounded-md border shadow-sm w-full max-w-sm group transition-all duration-200 hover:translate-y-2.5 hover:shadow-md dark:border-gray-800 dark:bg-gray-800">

        <img :src="event.imagen ? '/storage/' + event.imagen : '/img/login-image.jpg'"
            alt="imagen evento" class="h-60 w-full rounded-t object-cover">

        <div class="px-4">
            <div class="mt-3 text-left space-y-2">
                <span class="text-sm font-light text-gray-800 dark:text-gray-400">{{ event.fecha_inicio }}</span>
                <h4 class="line-clamp-2 h-14 mt-2 text-lg font-semibold group-hover:text-blue-500 text-gray-800 dark:text-white">
                    {{ event.nombre }}
                </h4>
            </div>

            <div v-if="showForm" class="mt-2 space-y-2">
                <input
                    v-model="email"
                    type="email"
                    placeholder="Tu correo"
                    class="w-full p-2 border rounded text-sm dark:bg-gray-700 dark:text-white"
                />
                <BaseButton :disabled="loading" @click="inscribirse" label="Confirmar inscripción" color="success" small />

                <!-- Mensajes -->
                <p v-if="message" class="text-green-500 text-sm">{{ message }}</p>
                <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
            </div>

            <div class="justify-between flex mt-4 gap-2">
                <BaseButton small :icon="mdiArrowRight" @click="$emit('openModal', event)" color="info" label="Conocer más" />
                <BaseButton small @click="showForm = !showForm" color="primary" label="Inscribirse" />
            </div>
        </div>
    </div>
</template>
