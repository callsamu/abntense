<script setup lang="ts">
import axios from 'axios';
import { ref, shallowReactive, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { AbntMetadata, DocumentData } from '@/types';
import { Editor, useEditor } from '@monorepo/editor';
import MenuButton from '@/Components/MenuButton.vue';
import TypstViewer from './Partials/TypstViewer.vue';
import EditMetadataForm from './Partials/EditMetadataForm.vue';
import { recordToReference, Reference, ReferenceRecord } from '@/lib/references';
import Dialog from '@/Components/Dialog.vue';
import ReferenceForm from '@/Components/Forms/ReferenceForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ArrowLeft, Eye, Plus, Save, Settings } from 'lucide-vue-next';

interface Props {
    document: DocumentData;
};

const props =  defineProps<Props>();

const doc = shallowReactive({
    ...props.document,
    references: (() => {
        const obj: {[key: string]: Reference} =  {}
        const records = props.document.references;

        for (const [id, record] of Object.entries(records)) {
            obj[id] = recordToReference(record);
        }

        return obj;
    })(),
});

let changed = true;

const previewOpen = ref(false);
const typst = ref<Uint8Array | null>(null);

const editor = useEditor({
    initialContent: doc.content,
});

watch(previewOpen, async (newPreviewOpen) => {
    if (newPreviewOpen) {
        save().then(compile);
        changed = false;
    }
});

async function save() {
    return axios.patch(route('document.update', doc.id), {
        content:  editor.value?.getJSON(),
    });
}

async function compile() {
    const resp = await axios.get(route('document.compile', { id: doc.id }), {
        responseType: 'arraybuffer',
        headers: {
            'Accept': 'application/octet-stream'
        }
    });

    typst.value = new Uint8Array(resp.data);
}

async function onMetadataUpdate(title: string, metadata: AbntMetadata) {
    doc.title = title;
    doc.metadata = metadata;
    previewOpen.value = true;
    changed = true;
    openMetadataDialog.value = false;
}

function referenceAdd(id: string, ref: Reference) {
    if (!ref) {
        throw new Error('reference is null');
    }

    doc.references = {
        [id]: ref,
        ...doc.references,
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
                :documentId="doc.id"
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
                :id="doc.id"
                :title="doc.title"
                :metadata="doc.metadata"
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
                {{ doc.title }}
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
            <div class="h-full py-4 px-6 max-w-xl text-neutral-200">
                <div class="inline-flex items-center text-neutral-200 text-sm gap-4 w-full">
                    <h3 class="font-semibold"> Referências </h3>
                    <button class="cursor-pointer hover:text-neutral-50" @click="openReferenceDialog = true">
                        <Plus :size="16" />
                    </button>
                </div>
                <div
                    v-for="[id, ref] in Object.entries(doc.references)"
                    class="flex flex-col items-start my-1 pl-2"
                >
                    <button
                        class="m-0 py-1 text-sm text-neutral-300"
                        @click="editor?.chain().focus().insertCitation(id, ref.label()).run()"
                    >
                        <p class="p-0 m-0 text-left hover:text-neutral-100 cursor-pointer">
                            {{ ref.label().trim() }}
                        </p>
                    </button>
                </div>
            </div>
            <div class="flex grow justify-center items-stretch h-full text-white">
                <div className="lg:w-2/5 md:w-2/3  h-full flex items-stretch flex-col">
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
