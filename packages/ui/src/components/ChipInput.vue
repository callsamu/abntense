<script setup="ts">
    import InputText from '@/volt/InputText.vue';
    import Button from '@/volt/Button.vue';
    import Chip from '@/volt/Chip.vue';
    import { ref, defineModel } from 'vue';


    const item = ref('');
    const itens = defineModel({ required: true });


    function onEnter() {
        itens.value = [...itens.value, item.value];
        item.value = null;
    }

    function onRemove(idx) {
        itens.value = itens.value.filter((_, i) => i !== idx);
    }
</script>

<template>
    <div class="p-4">
        <div class="flex gap-2 flex-wrap text-neutral-700 dark:text-neutral-0">
            <Chip
                :data-test="`chip-${idx}`"
                v-for="(item_, idx) in itens"
                :key="item_"
                :label="item_"
                pt:root:class="
                    bg-neutral-100 dark:bg-neutral-800 pl-2
                "
                removable
            >
                <template #removeicon>
                    <i
                        data-test="remove"
                        style="font-size: 0.75rem"
                        class="pi pi-times cursor-pointer text-base w-4 rounded-full text-surface-800 dark:text-surface-0 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-primary"
                        @click="onRemove(idx) && removeicon.removeCallback()"
                    />
                </template>
            </Chip>
        </div>
        <form @submit.prevent="onEnter" class="mt-5 flex gap-2 h-10">
            <InputText
                data-test="input"
                v-model="item"
                pt:root:class="rounded-md border-neutral-300 focus:border-primary-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:focus:border-neutral-600 "
            />
            <Button
                icon="pi pi-plus"
                data-test="add"
                type="submit"
                pt:root:class="rounded-md bg-primary text-neutral-500 border-neutral-300 dark:bg-neutral-900 dark:text-neutral-300 hover:text-neutral-600"
            />
        </form>
    </div>
</template>
