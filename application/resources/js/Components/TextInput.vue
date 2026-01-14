<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="
            rounded-md border border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500
            dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:focus:border-indigo-600 focus:outline-none
            dark:focus:ring-indigo-600 py-1 px-2
        "
        v-model="model"
        ref="input"
    />
</template>
