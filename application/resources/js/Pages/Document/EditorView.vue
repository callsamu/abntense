<script setup lang="ts">
import { defineProps, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { AbntMetadata, DocumentData } from '@/types';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import setupEditorExtensions from '@monorepo/editor';
import axios from 'axios';
import MenuButton from '@/Components/MenuButton.vue';
import PDFViewer from './Partials/PDFViewer.vue';
import EditMetadataForm from './Partials/EditMetadataForm.vue';


interface Props {
    document: DocumentData;
};

const props = defineProps<Props>();
let changed = true;

const editor = useEditor({
    content: props.document.content,
    onUpdate: () => {
        changed = true;
    },
    extensions: setupEditorExtensions({
       placeholderClass: '.empty-node',
    }),
})

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
        content: editor.value?.getJSON(),
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
                <editor-content
                    class="overflow-y-scroll grow py-4 pr-4"
                    :editor="editor"
                    spellcheck="false"
                />
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

<style>
    @reference "@monorepo/ui/app.css";

    .tiptap {
        height: 100%;
        color: white;
        overflow-y: scroll;
        outline: none;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .tiptap h1 {
        @apply font-bold text-3xl mt-5 mb-1;
    }

    .tiptap h2 {
        @apply font-bold text-2xl mt-4 mb-1;
    }

    .tiptap h3 {
        @apply font-bold text-xl mt-3 mb-1;
    }

    .tiptap p {
        @apply my-1;
    }

    .tiptap ul {
        @apply pl-6 my-1 list-disc;
    }

    .tiptap li {
       @apply my-1 pl-1;
    }

    .tiptap {
        @apply text-sm;
    }

    .tiptap p.is-empty::before {
        @apply opacity-70;
        content: attr(data-placeholder);
        float: left;
        height: 0;
        pointer-events: none;
    }
</style>

