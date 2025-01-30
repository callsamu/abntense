<script setup lang="ts">
import { Icon } from '@iconify/vue';

import SecondaryButton from './SecondaryButton.vue';

defineProps<{
    icon: string;
    title: string;
    active?: boolean;
}>();

</script>

<template>
    <SecondaryButton
        :aria-selected="active"
        :title="title"
        class="
            w-full m-0 border-none rounded-none px-5 py-4
            focus:ring-0 focus:ring-offset-0
            aria-selected:bg-neutral-50 dark:aria-selected:bg-neutral-700
        ">
        <Icon :icon="icon" class="w-7 h-7" />
    </SecondaryButton>
</template>
