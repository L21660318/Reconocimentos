<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { mdiAccount, mdiContentSave, mdiClose } from "@mdi/js";
import HeadLogo from "@/Components/HeadLogo.vue";

const props = defineProps({
    title: String,
    routeName: String,
    event: Object,
    users: Array,
    assigned: Array,
});

const form = useForm({
    users: props.assigned,
});

const saveForm = () => {
    form.post(route(`${props.routeName}storeUsers`, props.event.id));
};
</script>

<template>
    <HeadLogo :title="title" />
    <LayoutMain>
        <SectionTitleLineWithButton :icon="mdiAccount" :title="title" main>
            <Link :href="route(`${routeName}index`)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x"
                    viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </Link>
        </SectionTitleLineWithButton>

        <CardBox isForm @submit.prevent="saveForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 max-h-[65vh] overflow-y-auto">
                <div v-for="user in users" :key="user.id" class="flex items-center space-x-2">
                    <input
                        type="checkbox"
                        :id="`user-${user.id}`"
                        :value="user.id"
                        v-model="form.users"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200"
                    />
                    <label :for="`user-${user.id}`" class="text-sm dark:text-white">
                        {{ user.name }} ({{ user.email }})
                    </label>
                </div>
            </div>

            <template #footer>
                <BaseButtons>
                    <BaseButton :routeName="`${routeName}index`" :icon="mdiClose" color="white" label="Cancelar" />
                    <BaseButton :icon="mdiContentSave" color="success" type="submit" label="Guardar" @click="saveForm" />
                </BaseButtons>
            </template>
        </CardBox>
    </LayoutMain>
</template>
