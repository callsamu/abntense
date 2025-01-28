<script setup lang="ts">
import { defineProps, ref, useTemplateRef, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { DocumentData } from '@/types';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import { Icon } from '@iconify/vue';
import StarterKit from '@tiptap/starter-kit';
import * as pdfJs from 'pdfjs-dist';
import axios from 'axios';
import NavLink from '@/Components/NavLink.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

pdfJs.GlobalWorkerOptions.workerSrc = '/build/pdf.worker.min.mjs';

interface Props {
    document: DocumentData;
};

const props = defineProps<Props>();

const editor = useEditor({
    content: props.document.content,
    extensions: [StarterKit],
})

const container = useTemplateRef<HTMLDivElement>('pdf-viewer');
const pdf = ref<string | null>(null);


watch(pdf, async (newPdf) => {
    if (!container.value) return;

    const containerRef = container.value;
    containerRef.innerHTML = '';
    const pdfDoc = await pdfJs.getDocument(newPdf).promise;

    const page = await pdfDoc.getPage(1);
    const viewport = page.getViewport({ scale: 1 });

    const pageNumber = pdfDoc.numPages;

    for (let i = 0; i < pageNumber; i++) {
        const newCanvas = document.createElement('canvas');
        newCanvas.width = viewport.width;
        newCanvas.height = viewport.height;

        const context = newCanvas.getContext('2d');
        if (!context) continue;
        const visiblePage = await pdfDoc.getPage(i + 1);
        visiblePage.render({ canvasContext: context, viewport });

        containerRef.appendChild(newCanvas);
    }
});

async function save() {
    console.info(props.document);
    console.log(editor.value?.getJSON());
    return axios.patch(route('document.update', props.document.id), {
        document: editor.value?.getJSON(),
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

</script>
<template>
    <Head :title=props.document.title />
    <div class="h-screen bg-neutral-100 dark:bg-neutral-900 flex">
        <div class="h-full w-fit flex flex-col gap-4 bg-neutral-800 px-2 py-4">
            <SecondaryButton
                class="flex-col border-none"
                title="Voltar para a Dashboard"
                @click="router.visit(route('dashboard'))"
            >
                <Icon icon="material-symbols:home" class="w-6 h-6" />
            </SecondaryButton>
            <SecondaryButton
                @click="save()"
                title="Salvar"
                class="border-none w-full"
            >
                <Icon icon="material-symbols:save" class="w-6 h-6" />
            </SecondaryButton>
            <SecondaryButton
                title="Preview"
                class="border-none w-full"
                @click="save().then(compile)"
            >
                <Icon icon="mdi:eye" class="w-6 h-6" />
            </SecondaryButton>
        </div>
        <div class="flex flex-grow justify-center items-stretch h-full text-white">
            <div className="w-2/5 p-5 h-full flex items-stretch flex-col overflow-y-scroll">
                <div class="flex flex-col border-b border-neutral-700 py-3">
                    <h2 class="text-4xl mb-6 font-bold">
                        {{ props.document.title }}
                    </h2>
                    <div class="flex gap-3">
                    </div>
                </div>
                <editor-content
                    class="overflow-y-scroll flex-grow py-4 pr-4"
                    :editor="editor"
                    spellcheck="false"
                />
            </div>
            <div class="
                bg-neutral-950 overflow-y-scroll
                border rounded-xl border-neutral-800 my-5
                flex flex-col
            " :class="{ 'hidden': pdf === null }">
                <div class="w-full bg-neutral-900 border-b border-neutral-800 rounded-t-xl p-4 flex justify-end">
                    <SecondaryButton @click="pdf = null">Close</SecondaryButton>
                </div>
                <div
                    ref="pdf-viewer"
                    class="w-full my-2 overflow-y-scroll p-10 flex-grow flex items-center flex-col gap-4">
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .tiptap {
        height: fit;
        color: white;
        overflow-y: scroll;
        outline: none;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .tiptap h1 {
        @apply text-3xl font-bold;
    }

    .tiptap h2 {
        @apply text-2xl font-bold;
    }
</style>

