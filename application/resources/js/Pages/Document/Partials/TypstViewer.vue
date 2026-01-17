<script setup lang="ts">
import { createTypstRenderer } from '@myriaddreamin/typst.ts';
import { onMounted, useTemplateRef, watch } from 'vue';
import rendererWasm from '@myriaddreamin/typst-ts-renderer/wasm?url'


const props = defineProps<{
    artifact: Uint8Array | null
}>();

const renderer = createTypstRenderer();
const container = useTemplateRef("container");

async function render(artifact: Uint8Array) {
    if (!artifact) return;

    if (!container.value) {
        console.error("unable to use container for rendering document");
        return;
    }

	await renderer.renderToCanvas({
		container: container.value,
		backgroundColor: "#ffffff",
		pixelPerPt: 2,
		artifactContent: artifact,
		format: "vector",
	});
}

onMounted(async () => {
    await renderer.init({
        getModule: () => rendererWasm,
    });

    if (props.artifact) render(props.artifact);
})

watch(
    () => props.artifact,
    (newArtifact) => newArtifact && render(newArtifact)
)

</script>

<template>
    <div ref="container" class="w-[512px]">
    </div>
</template>
