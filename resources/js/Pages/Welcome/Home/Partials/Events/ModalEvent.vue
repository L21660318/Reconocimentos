<script setup>
import BaseIcon from "@/Components/BaseIcon.vue";
import Modal from '@/Components/Modal.vue';
import moment from "moment";
import { inject, ref } from 'vue';
import { mdiCalendarRange, mdiCalendarRemove, mdiFilePdfBox, mdiClose } from "@mdi/js";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";

const { showModal, setShowModal } = inject('showModal');

defineProps({
    event: Object
});

const showPdf = ref(false);

const togglePdf = () => {
    showModal.value = !showModal.value;
    showPdf.value = !showPdf.value;
};
</script>

    <template>
    <Modal :show="showModal" @close="setShowModal(false)" :closeable="true">
        <div class="px-4 mb-4">
            <img :src="event.imagen ? '/storage/' + event.imagen : '/img/login-image.jpg'"
                class="rounded mt-8 pt-4 w-full h-80 object-cover" alt="imagen evento" />

            <div class="my-4 pr-12">
                <h2 class="text-left text-lg font-bold">{{ event.nombre }}</h2>
            </div>

            <div class="mb-2 flex justify-between text-gray-700">
                <BaseIcon :path="mdiCalendarRange" size="15" />
                <span class="text-gray-600">Fecha de inicio</span>
                <span class="ml-auto text-black font-bold">{{ moment(event.fecha_inicio).format("DD-MM-YYYY") }}</span>
            </div>

            <div class="mb-2 flex justify-between text-gray-700">
                <BaseIcon :path="mdiCalendarRemove" size="15" />
                <span class="text-gray-600">Fecha de cierre</span>
                <span class="ml-auto text-black font-bold">{{ moment(event.fecha_fin).format("DD-MM-YYYY") }}</span>
            </div>

            <div v-if="event.archivo_pdf" class="mb-4 flex justify-between text-gray-700">
                <BaseIcon :path="mdiFilePdfBox" size="15" />
                <span class="text-gray-600"> Archivo </span>
                <a @click="togglePdf()"
                    class="ml-auto text-blue-500 underline font-bold transition hover:cursor-pointer hover:text-blue-600">
                    Ver PDF
                </a>
            </div>

            <hr class="border-t-2 border-blue-300 my-4" />

            <div>
                <p class="text-base text-justify text-gray-500"> {{ event.descripcion || 'Sin descripción.' }} </p>
            </div>
        </div>
    </Modal>

    <div v-if="showPdf"
        class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50 bg-slate-800 bg-opacity-50"
        tabindex="0" @keydown.esc="togglePdf()">
        <div class="relative w-full max-w-4xl mx-auto">
            <CardBox class="mt-5" :is-modal="true">
                <div class="justify-between flex p-2">
                    <span class="font-bold text-sm">
                        Archivo del evento
                    </span>
                    <BaseButton color="danger" :icon="mdiClose" small @click="togglePdf()" />
                </div>
                <div class="overflow-hidden rounded-md">
                    <iframe :src="'/storage/' + event.archivo_pdf" class="w-full h-[75vh]" />
                </div>
            </CardBox>
        </div>
    </div>
</template>
