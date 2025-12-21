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
        content: $props.initialContent ?? '<span class="filler"></span><p>haha</p>',
        onUpdate: ({ editor }) => {
            const json = editor.getJSON();
            $emit('update', json);
            console.log(json);
        },
        extensions: setupEditorExtensions({
           placeholderClass: '.empty-node',
        }),
        enableContentCheck: true,
        onContentError({ error }) {
            console.error(error);
        }
    });

    function addPretextual(name: string, content: string) {
        if (!editor.value) {
            console.error("editor not initialized");
            return;
        }

        const $elements = editor.value.$doc.querySelectorAll('pretextual_element');
        const $element = $elements[0];

        const msg = `<details><summary>${name}</summary><p>${content}</p></details>`;
        editor.value.chain()
            .focus('end')
            .insertContentAt($element?.after ?? 1, msg, {updateSelection: true})
            .run();
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
                },
            ],
        },
        {
            label: 'Elementos Pré-Textuais',
            items: [
                {
                    label: 'Resumo',
                    icon: 'iconoir:page',
                    command: () => addPretextual('Resumo', 'Resumo do seu Trabalho')
                },
                {
                    label: 'Agradecimentos',
                    icon: 'mdi:love',
                    command: () => addPretextual('Agradecimentos', 'Agradeço a fulano e cicrana')
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
