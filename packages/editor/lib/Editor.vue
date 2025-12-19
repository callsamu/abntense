<script setup lang="ts">
    import setupEditorExtensions from './extensions';
    import { FloatingMenu } from '@tiptap/vue-3/menus';
    import { useEditor, EditorContent } from '@tiptap/vue-3';
    import type { JSONContent } from '@tiptap/vue-3';
    import { Menu } from '@monorepo/ui';
    import { ref } from 'vue';

    const $props = defineProps([ 'initialContent' ]);
    const $emit = defineEmits<{
        (e: 'update', value: JSONContent): void
    }>();

    const editor = useEditor({
        content: $props.initialContent,
        onUpdate: ({ editor }) => {
            $emit('update', editor.getJSON());
        },
        extensions: setupEditorExtensions({
           placeholderClass: '.empty-node',
        }),
    });

    function insertSummary() {
        console.log("sum");
        if (!editor.value) return;
        console.log(editor.value.getHTML());
        const content = 'Esse é um componente obrigatório e deve ser feito em um único parágrafo contendo de 150 a 500 palavras. É necessário ainda que o texto esteja na terceira pessoa do singular e em voz ativa.';
        const msg = `<details><summary>Resumo</summary><p>${content}</p></details>`;
        editor.value.chain().focus().insertContentAt(0, msg).run();
    }

    const items = ref([
        {
            label: 'Texto',
            items: [
                {
                    label: 'Título',
                    icon: "cuida:heading1-outline",
                    command: () => editor.value?.chain().focus().setHeading({ level: 1 }).run()
                },
                {
                    label: 'Subtítulo',
                    icon: "cuida:heading2-outline",
                    command: () => editor.value?.chain().focus().setHeading({ level: 2 }).run()
                }
            ],
        },
        {
            label: 'Elementos Pré-Textuais',
            items: [
                {
                    label: 'Resumo',
                    icon: 'ic:sharp-subtitles',
                    command: () => insertSummary()
                }
            ],
        },
    ])

</script>

<template>
    <div class="editor-container w-full">
        <floating-menu
            :editor="editor"
            v-if="editor"
            :options="{
                placement: 'right',
                strategy: 'absolute',
                offset: 120,
            }">
                <div class="w-64">
                    <Menu :model="items" />
                </div>
        </floating-menu>
        <editor-content
            class="overflow-y-scroll grow py-4 pr-4"
            :editor="editor"
            spellcheck="false"
        />
    </div>
</template>
