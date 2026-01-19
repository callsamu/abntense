<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import { AbntMetadata, DocumentData } from '@/types';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const emit = defineEmits({
    'update': (title: string, metadata: AbntMetadata) => {
        return true;
    }
});

const props = defineProps<{
    id: number;
    title: string;
    metadata: AbntMetadata;
}>();

const form = useForm({
    title: props.title,
    metadata: props.metadata,
});

async function update() {
    const resp = await axios.patch(route('document.update', props.id), {
        title: form.title,
        metadata: form.metadata,
    });

    const doc: DocumentData = resp.data;

    if (resp.status === 200) {
        emit('update', doc.title, doc.metadata);
    }
};
</script>

<template>
    <form @submit.prevent="update()" class="grid grid-cols-2 gap-4 px-6">
        <div class="w-full col-span-2">
            <InputLabel for="title" value="Título" />
            <TextInput
                id="title"
                ref="title"
                v-model="form.title"
                class="w-full"
            />
        </div>
        <div class="col-span-2">
            <InputLabel for="location" value="Descrição" />
            <TextArea
                id="description"
                v-model="form.metadata.description"
                spellcheck="false"
                rows="4"
                class="w-full bg resize-none"
            />
        </div>
        <div>
            <InputLabel for="location" value="Local" />
            <TextInput
                id="location"
                v-model="form.metadata.location"
                class="mt-1 block w-full"
            />
        </div>
        <div>
            <InputLabel for="institution" value="Instituição" />
            <TextInput
                id="Institution"
                v-model="form.metadata.institution"
                class="mt-1 block w-full"
            />
        </div>
        <div></div>

        <div class="w-full col-span-2 flex justify-end">
            <PrimaryButton class="my-2 mb-4 w-fit px-2" :disabled="form.processing">Salvar</PrimaryButton>
        </div>
    </form>
</template>
