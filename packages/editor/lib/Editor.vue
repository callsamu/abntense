<script setup lang="ts">
import { EditorContent } from '@tiptap/vue-3';
import { SlashMenu } from './extensions/SlashCommands/components';
import { generateMenuItems } from './extensions/SlashCommands';
import { BubbleMenu } from '@tiptap/vue-3/menus';
import { Icon } from '@iconify/vue';
import {
  ToolbarRoot,
  ToolbarToggleGroup,
  ToolbarToggleItem,
} from 'reka-ui'
import type { Editor } from '@tiptap/vue-3';

const { editor }= defineProps<{ editor: Editor }>();

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
        key: "lista",
        icon: "carbon:list",
        label: "Lista Numerada",
        command: (editor, range) => editor.chain().focus().deleteRange(range).toggleBulletList().run()
    },
    {
        key: "listanumerada",
        icon: "carbon:list-numbered",
        label: "Lista Numerada",
        command: (editor, range) => editor.chain().focus().deleteRange(range).toggleOrderedList().run()
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

const marksMenu = [
    {
        icon: 'carbon:text-bold',
        mark: 'bold',
    },
    {
        icon: 'carbon:text-italic',
        mark: 'italic',
    },
    {
        icon: 'carbon:text-underline',
        mark: 'underline',
    },
];

</script>

<template>
    <SlashMenu :editor="editor" :items="items">
        <BubbleMenu :editor="editor">
            <ToolbarRoot
            class="bg-neutral-800 flex w-full !min-w-max rounded-lg shadow-sm overflow-hidden border border-neutral-700"
            aria-label="Formatting options"
            >
                <ToolbarToggleGroup
                  type="multiple"
                  aria-label="Text formatting"
                >
                    <ToolbarToggleItem
                        v-for="opts in marksMenu"
                        class="
                            flex-shrink-0 flex-grow-0 basis-auto py-2 px-4
                            inline-flex leading-none items-center justify-center
                            outline-none hover:bg-neutral-700 hover:text-neutral-200 focus:relative
                            cursor-pointer
                            data-[state='on']:bg-neutral-700 data-[state='on']:text-neutal-200"
                        :value="opts.mark"
                        :data-state="editor.isActive(opts.mark) ? 'on' : 'off'"
                        @click="editor.chain().focus().toggleMark(opts.mark).run()"
                        :aria-label="opts.mark"
                    >
                        <Icon :icon="opts.icon" />
                    </ToolbarToggleItem>
                </ToolbarToggleGroup>
            </ToolbarRoot>
        </BubbleMenu>
        <editor-content
            class="h-full overflow-y-scroll grow py-4 pr-4"
            :editor="editor"
            spellcheck="false"
        />
    </SlashMenu>
</template>
