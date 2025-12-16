<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { defineProps, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { AbntMetadata, DocumentData } from '@/types';
import { Editor } from '@monorepo/editor';
import axios from 'axios';
import MenuButton from '@/Components/MenuButton.vue';
import PDFViewer from './Partials/PDFViewer.vue';
import EditMetadataForm from './Partials/EditMetadataForm.vue';


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
const tab = ref(Tabs.Editor);
const pdf = ref<string | null>(null);

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
    const resp = await axios.get(route('document.compile', props.document.id), {
        responseType: 'arraybuffer',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/pdf'
        }
    });

    const blob = new Blob([resp.data]);
    const reader = new FileReader();
    reader.readAsDataURL(blob);
    reader.onload = () => {
        pdf.value = reader.result as string;
    }
}

async function onMetadataUpdate(title: string, metadata: AbntMetadata) {
    props.document.title = title;
    props.document.metadata = metadata;
    previewOpen.value = true;
    tab.value = Tabs.Editor;
    changed = true;
}

function handleUpdate(content: typeof props.document.content) {
    changed = true;
    props.document.content = content;
}


</script>
<template>
    <Head :title=props.document.title />
    <div class="h-screen bg-neutral-100 dark:bg-neutral-900 flex">
        <div class="flex flex-row">
            <div class="h-full w-fit flex flex-col bg-neutral-800">
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
                    @click="previewOpen = !previewOpen; tab = Tabs.Editor"
                />
                <MenuButton
                    icon="solar:document-add-linear"
                    title="Editar Informações"
                    :active="tab === Tabs.Settings"
                    @click="tab = tab === Tabs.Settings ? Tabs.Editor : Tabs.Settings"
                />
            </div>
            <div
                v-if="tab === Tabs.Settings"
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
                <div class="flex flex-col border-b border-neutral-700 py-3">
                    <h2 class="text-4xl mb-6 font-bold">
                        {{ props.document.title }}
                    </h2>
                    <div class="flex gap-3">
                    </div>
                </div>
                <div class="editor-container">
                    <Editor
                        :initialContent="props.document.content"
                        @update="handleUpdate($event)"
                    />
                </div>
            </div>
            <div class="
                bg-neutral-950 overflow-y-scroll
                border rounded-xl border-neutral-800 my-5
                flex flex-col w-2/5
            " :class="{ 'hidden': !previewOpen || tab !== Tabs.Editor }">
                <PDFViewer
                    :pdf="pdf"
                    @reload="save().then(compile)"
                    @close="previewOpen = false"
                />
            </div>
        </div>
    </div>
</template>
