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
import { ArrowLeft, Eye, Save, Settings } from 'lucide-vue-next';

interface Props {
    document: DocumentData;
};

const props = defineProps<Props>();

let changed = true;

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

    typst.value = new Uint8Array(resp.data);
}

async function onMetadataUpdate(title: string, metadata: AbntMetadata) {
    props.document.title = title;
    props.document.metadata = metadata;
    previewOpen.value = true;
    changed = true;
    openMetadataDialog.value = false;
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
const openMetadataDialog = ref(false);

</script>
<template>
    <Head :title=props.document.title />
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
    <Dialog v-model:open="openMetadataDialog">
        <template #title>
            Editar Informações
        </template>
        <template #content>
            <EditMetadataForm
                :id="props.document.id"
                :title="props.document.title"
                :metadata="props.document.metadata"
                @update="onMetadataUpdate"
            />
        </template>
    </Dialog>
    <div class="flex flex-col h-screen bg-neutral-100 dark:bg-neutral-900 text-neutral-100">
        <div class="flex text-sm bg-neutral-800 text-neutral-100 justify-start items-center shadow-md">
            <MenuButton
                title="Voltar"
                class="pr-4"
                @click="router.visit(route('dashboard'))"
            >
                <ArrowLeft :size="20"/>
            </ MenuButton>
            <p class="px-4 font-semibold uppercase text-xs tracking-widest opacity-80">
                {{ props.document.title }}
            </p>
            <MenuButton
                title="Preview"
                :active="previewOpen"
                @click="previewOpen = !previewOpen"
            >
                <Eye :size="20"/>
                Preview
           </MenuButton>
           <MenuButton
                title="Salvar"
                @click="save()"
            >
                <Save :size="20"/>
                Salvar
            </MenuButton>
            <MenuButton
                title="Editar Informações"
                @click="openMetadataDialog = true"
            >
                <Settings />
                Configurações
            </MenuButton>
        </div>
        <div class="flex grow h-full overflow-clip">
            <div class="flex grow justify-center items-stretch h-full text-white">
                <div className="w-2/5 p-5 h-full flex items-stretch flex-col">
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
    </div>
</template>
