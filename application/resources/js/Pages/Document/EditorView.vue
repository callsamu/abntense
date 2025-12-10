<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { defineProps, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { AbntMetadata, DocumentData } from '@/types';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import { FloatingMenu } from '@tiptap/vue-3/menus';
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

function insertSummary() {
    console.log("sum");
    if (!editor.value) return;
    console.log(editor.value.getHTML());
    const content = 'Esse é um componente obrigatório e deve ser feito em um único parágrafo contendo de 150 a 500 palavras. É necessário ainda que o texto esteja na terceira pessoa do singular e em voz ativa.';
    const msg = `<details><summary>Resumo</summary><p>${content}</p></details>`;
    editor.value.chain().focus().insertContentAt(0, msg).run();
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
                    <floating-menu
                        :editor="editor"
                        v-if="editor"
                        :options="{
                            placement: 'right',
                            strategy: 'absolute',
                            offset: 120,
                        }">
                            <div class="border border-neutral-700 bg-neutral-800 text-sm rounded-xl py-2 px-1 w-64">
                            <ul>
                                <li>
                                    <button class="w-full rounded-xl hover:bg-neutral-700 cursor-pointer py-1 px-4 text-left flex items-center gap-3"
                                        @click="insertSummary()"
                                    >
                                        <Icon icon="material-symbols-light:contextual-token-outline" class="w-3 h-3" />
                                        Adicionar Resumo
                                    </button>
                                </li>
                                <li>
                                    <button class="w-full rounded-xl hover:bg-neutral-700 cursor-pointer py-1 px-4 text-left flex items-center gap-3">
                                        <Icon icon="cuida:heading1-outline" class="w-3 h-3" />
                                        Título
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </floating-menu>
                    <editor-content
                        class="overflow-y-scroll grow py-4 pr-4"
                        :editor="editor"
                        spellcheck="false"
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

    .details {
        @apply border border-neutral-700;
        display: flex;
        gap: 0.25rem;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
        padding: 0.5rem;
    }

    .details summary {
        @apply font-bold text-xl list-none;
    }

    .details summary::after {
        @apply mx-2 opacity-50;
        content: "...";
    }

    .details.is-open > summary::after {
        display: none;
    }

    .details > button {
      align-items: center;
      background: transparent;
      border-radius: 4px;
      display: flex;
      font-size: 0.625rem;
      height: 1.25rem;
      justify-content: center;
      line-height: 1;
      margin-top: 0.1rem;
      width: 1.25rem;
      @apply mx-2 pt-4 cursor-pointer;
    }

    .details > button::before {
       content: '\25B6';
    }

    .details.is-open > button::before {
      transform: rotate(90deg);
    }

    .details > div {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      width: 100%;
      @apply py-2 mr-4 text-justify;
    }

    .details > [data-type='detailsContent'] {
        @apply bg-red-800;
    }

    .details > [data-type='detailsContent'] > :last-child {
        @apply mb-1;
    }

    .details .details {
      margin: 0.5rem 0;
    }
</style>

