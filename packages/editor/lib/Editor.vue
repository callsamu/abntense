<script setup lang="ts">
import setupEditorExtensions from './extensions';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import type { JSONContent } from '@tiptap/vue-3';
import { SlashMenu } from './extensions/SlashCommands/components';
import { enableKeyboardNavigation, generateMenuItems } from './extensions/SlashCommands';

const $props = defineProps([ 'initialContent' ]);
const $emit = defineEmits<{
    (e: 'update', value: JSONContent): void
}>();


const editor = useEditor({
    content: $props.initialContent ?? '<span class="filler"></span><p>hahahahe</p>',
    onUpdate: ({ editor }) => {
        const json = editor.getJSON();
        $emit('update', json);
    },
    extensions: setupEditorExtensions({
       placeholderClass: '.empty-node',
    }),
    enableContentCheck: true,
    editorProps: {
        handleDOMEvents: {
            keydown: (_, v) => enableKeyboardNavigation(v),
        }
    }
});

const items = generateMenuItems([
    {
        key: "titulo",
        icon: "cuida:heading1-outline",
        label: 'Título',
        command: (editor, range) => editor.chain().focus().deleteRange(range).setHeading({ level: 1 }).run()
    },
    {
        key: "subtitulo",
        icon: "cuida:heading2-outline",
        label: 'Subtítulo',
        command: (editor, range) => editor.chain().focus().deleteRange(range).setHeading({ level: 2 }).run()
    },
    {
        key: "paragrafo",
        icon: "carbon:paragraph",
        label: 'Parágrafo',
        command: (editor, range) => editor.chain().focus().deleteRange(range).setParagraph().run()
    },
    {
        key: "notaderodape",
        icon: "carbon:text-footnote",
        label: 'Nota de Rodapé',
        command: (editor, range) => editor.chain().focus().deleteRange(range).insertFootnote().run()
    },
    {
        key: "resumo",
        icon: "carbon:text-long-paragraph",
        label: 'Resumo',
        command: (editor, range) => editor.
            chain().
            focus().
            deleteRange(range).
            insertPretextualElement("Resumo", "elemento de resumo").
            run()
    },
]);
</script>

<template>
    <div v-if="editor" class="editor-container w-full">
        <SlashMenu :editor="editor" :items="items">
            <editor-content
                class="h-full overflow-y-scroll grow py-4 pr-4"
                :editor="editor"
                spellcheck="false"
            />
        </SlashMenu>
    </div>
</template>
