<script setup lang="ts">
import { Icon } from '@iconify/vue';

import SecondaryButton from './SecondaryButton.vue';

defineProps<{
    title: string;
    active?: boolean;
}>();

</script>

<template>
    <button
        :aria-selected="active"
        :title="title"
        class="
            inline-flex items-center px-4 py-2 m-0 gap-3
            border-none rounded-none cursor-pointer
            uppercase font-semibold text-xs tracking-widest
            hover:bg-neutral-700 text-neutral-300
            transition duration-150 ease-in-out
            aria-selected:bg-neutral-50 dark:aria-selected:bg-neutral-700
    ">
        <slot></slot>
    </button>
</template>
