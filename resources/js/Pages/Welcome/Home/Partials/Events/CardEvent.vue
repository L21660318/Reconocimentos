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
    <div class="pb-6 rounded-xl border-2 border-gray-200/80 shadow-sm w-full max-w-sm group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-600/50 dark:bg-gray-800/50 backdrop-blur-sm overflow-hidden hover:ring-4 hover:ring-primary-500/10 hover:border-primary-500/30">
        <!-- Image with gradient overlay -->
        <div class="relative overflow-hidden h-60 w-full">
            <img 
                :src="event.imagen ? '/storage/' + event.imagen : '/img/login-image.jpg'"
                alt="imagen evento" 
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
            <!-- Date badge -->
            <div class="absolute top-4 right-4 bg-white/90 dark:bg-gray-900/90 text-gray-900 dark:text-white text-xs font-medium px-3 py-1 rounded-full shadow">
                {{ event.fecha_inicio }}
            </div>
        </div>

        <div class="px-5">
            <div class="mt-4 text-left space-y-2">
                <h4 class="line-clamp-2 h-14 text-xl font-bold group-hover:text-primary-600 text-gray-800 dark:text-white transition-colors">
                    {{ event.nombre }}
                </h4>
                <p v-if="event.descripcion" class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2">
                    {{ event.descripcion }}
                </p>
            </div>

            <!-- Registration form -->
            <div v-if="showForm" class="mt-4 space-y-3 animate-fade-in">
                <div>
                    <input
                        v-model="email"
                        type="email"
                        placeholder="Tu correo electrónico"
                        class="w-full p-3 border rounded-lg text-sm dark:bg-gray-700/50 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                    />
                </div>
                <BaseButton 
                    :disabled="loading" 
                    @click="inscribirse" 
                    label="Confirmar inscripción" 
                    color="success" 
                    small 
                    class="w-full justify-center"
                />

                <!-- Messages -->
                <div v-if="message" class="p-3 bg-green-100/80 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-sm rounded-lg">
                    {{ message }}
                </div>
                <div v-if="error" class="p-3 bg-red-100/80 dark:bg-red-900/50 text-red-700 dark:text-red-300 text-sm rounded-lg">
                    {{ error }}
                </div>
            </div>

            <!-- Action buttons -->
            <div class="flex mt-6 gap-3">
                <BaseButton 
                    small 
                    :icon="mdiArrowRight" 
                    @click="$emit('openModal', event)" 
                    color="info" 
                    label="Detalles" 
                    class="flex-1 justify-center hover:shadow-lg"
                />
                <BaseButton 
                    small 
                    @click="showForm = !showForm" 
                    color="primary" 
                    :label="showForm ? 'Cancelar' : 'Inscribirse'" 
                    class="flex-1 justify-center hover:shadow-lg"
                />
            </div>
        </div>
    </div>
</template>

<style>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>