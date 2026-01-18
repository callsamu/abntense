<script setup lang="ts">
import axios from 'axios';
import { Icon } from '@iconify/vue';
import { defineProps, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { AbntMetadata, DocumentData } from '@/types';
import { Editor, useEditor } from '@monorepo/editor';
import MenuButton from '@/Components/MenuButton.vue';
import TypstViewer from './Partials/TypstViewer.vue';
import EditMetadataForm from './Partials/EditMetadataForm.vue';
import { Reference } from '@/lib/references';
import Dialog from '@/Components/Dialog.vue';
import ReferenceForm from '@/Components/Forms/ReferenceForm.vue';

interface Props {
    document: DocumentData;
};

const props = defineProps<Props>();

let changed = true;

enum Tabs {
    Editor = 0,
    Settings = 1
}

const previewOpen = ref(false);
const typst = ref<Uint8Array | null>(null);

const editor = useEditor({
    initialContent: props.document.content,
});

watch(previewOpen, async (newPreviewOpen) => {
    if (newPreviewOpen && changed) {
        save().then(compile);
        changed = false;
    }
});

async function save() {
    return axios.patch(route('document.update', props.document.id), {
        content:  props.document.content,
    });
}

async function compile() {
    const resp = await axios.get(route('document.compile', { id :props. document.id }), {
        responseType: 'arraybuffer',
        headers: {
            'Accept': 'application/octet-stream'
        }
    });

    console.log(resp);
    typst.value = new Uint8Array(resp.data);
}

async function onMetadataUpdate(title: string, metadata: AbntMetadata) {
    props.document.title = title;
    props.document.metadata = metadata;
    previewOpen.value = true;
    changed = true;
}

function handleUpdate(content: typeof props.document.content) {
    changed = true;
    props.document.content = content;
}

function referenceAdd(id: string, ref: Reference) {
    if (!ref) {
        throw new Error('reference is null');
    }
    console.log(ref);
    props.document.references = {
        [id]: ref,
        ...props.document.references,
    }

    openReferenceDialog.value = false;
}

const openReferenceDialog = ref(false);

</script>
<template>
    <Head :title=props.document.title />

    <div class="h-screen bg-neutral-100 dark:bg-neutral-900 flex">
        <Dialog v-model:open="openReferenceDialog">
            <template #title>
                Adicionar Referência
            </template>
            <template #content>
                <ReferenceForm
                    @submit="referenceAdd"
                    :document="props.document"
                    class="mx-8 mb-8"
                />
            </template>
        </Dialog>
        <div class="flex flex-row">
            <div class="h-full w-fit flex flex-col bg-neutral-800">
                <MenuButton
                    icon="carbon:notebook"
                    title="Bibliography"
                    @click="openReferenceDialog = true"
                />
                <MenuButton
                    icon="material-symbols:home"
                    title="Home"
                    @click="router.visit(route('dashboard'))"
                />
                <MenuButton
                    icon="material-symbols:save"
                    title="Salvar"
                    @click="save()"
                />
                <MenuButton
                    icon="mdi:eye"
                    title="Preview"
                    :active="previewOpen"
                    @click="previewOpen = !previewOpen"
                />
            </div>
            <div
                v-if="false"
                class="w-96 bg-neutral-900 p-10 text-neutral-900 dark:text-neutral-100 border-2 border-neutral-800"
            >
                <EditMetadataForm
                    :id="props.document.id"
                    :title="props.document.title"
                    :metadata="props.document.metadata"
                    @update="onMetadataUpdate"
                />
            </div>
        </div>
        <div class="flex grow justify-center items-stretch h-full text-white">
            <div className="w-2/5 p-5 h-full flex items-stretch flex-col">
                <div class="flex flex-col py-3">
                    <h2 class="text-4xl mb-6 font-bold">
                        {{ props.document.title }}
                    </h2>
                    <div class="flex gap-3">
                    </div>
                </div>
                <div v-if="editor" class="editor-container">
                    <Editor :editor="editor" />
                </div>
            </div>
            <div class="
                bg-neutral-950 overflow-y-scroll
                border rounded-xl border-neutral-800 my-5
                flex flex-col w-2/5
            " :class="{ 'hidden': !previewOpen }">
                <TypstViewer
                    :artifact="typst"
                />
            </div>
        </div>
    </div>
</template>
