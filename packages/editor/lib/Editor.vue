<script setup lang="ts">
    import setupEditorExtensions from './extensions';
    import { FloatingMenu } from '@tiptap/vue-3/menus';
    import { useEditor, EditorContent } from '@tiptap/vue-3';
    import type { JSONContent } from '@tiptap/vue-3';

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
                <div class="border border-neutral-700 bg-neutral-800 text-sm rounded-xl py-2 px-1 w-64">
                <ul>
                    <li>
                        <button @click="insertSummary" class="w-full rounded-xl hover:bg-neutral-700 cursor-pointer py-1 px-4 text-left flex items-center gap-3"
                        >
                            Adicionar Resumo
                        </button>
                    </li>
                    <li>
                        <button class="w-full rounded-xl hover:bg-neutral-700 cursor-pointer py-1 px-4 text-left flex items-center gap-3">
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
</template>
