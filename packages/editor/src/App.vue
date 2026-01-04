<script setup lang="ts">
    import Editor from '../lib/Editor.vue';
    import useEditor from '../lib/useEditor';

    const editor = useEditor({});
</script>

<template>
    <div class="w-full h-screen flex justify-center items-center">
        <div v-if="editor" class="w-1/2 h-full bg-neutral-800 text-neutral-100 flex p-2 px-8">
            <Editor :editor="editor" />
        </div>
    </div>
</template>


