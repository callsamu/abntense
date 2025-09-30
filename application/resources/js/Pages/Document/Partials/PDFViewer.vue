<script setup lang="ts">
import { watch, useTemplateRef, defineProps } from 'vue';
import * as pdfJs from 'pdfjs-dist';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import pdfjsWorker from '/public/pdf.worker.min.js?url';

const { pdf } = defineProps<{ pdf: string | null }>();
const emit = defineEmits(['close', 'reload']);

const container = useTemplateRef<HTMLDivElement>('pdf-viewer');
pdfJs.GlobalWorkerOptions.workerSrc =  pdfjsWorker;

watch(() => pdf, async (newPdf) => {
    if (!container.value || !newPdf) return;

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

</script>
<template>
    <div class="w-full bg-neutral-900 border-b border-neutral-800 rounded-t-xl p-4 flex justify-end">
        <SecondaryButton @click="$emit('reload')">Reload</SecondaryButton>
    </div>
    <div
        ref="pdf-viewer"
        class="w-full my-2 overflow-y-scroll p-10 grow flex items-center flex-col gap-4">
    </div>
</template>

