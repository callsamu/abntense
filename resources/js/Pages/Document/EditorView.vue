<script setup lang="ts">
import { defineProps } from 'vue'
import { Head } from '@inertiajs/vue3'
import { DocumentData } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';

interface Props {
    document: DocumentData;
};

const props = defineProps<Props>();

const editor = useEditor({
    content: props.document.content,
    extensions: [StarterKit],
})
</script>
<template>
    <Head :title=props.document.title />

    <AuthenticatedLayout>
        <div class="
            flex justify-center items-center w-full
            overflow-y-scroll my-10

            text-white
        ">
            <div class="max-w-2xl">
                <h2 class="text-4xl py-2 mb-6 font-bold border-b border-neutral-700">
                    {{ props.document.title }}
                </h2>
                <editor-content
                    :editor="editor"
                    spellcheck="false"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
    .tiptap {
        height: 100vh;
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

