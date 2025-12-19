<template>
    <Menu
        ref="el"
        unstyled
        :pt="theme"
        :ptOptions="{
            mergeProps: ptViewMerge
        }"
    >
        <template #itemicon="{ item, class: iconClass }">
            <!-- Example: Use an inline SVG based on item properties -->
            <Icon v-if="item.icon" :icon="item.icon" />
            <!-- Fallback to default class for other items -->
            <span v-else :class="iconClass"></span>
        </template>
        <template v-for="(_, slotName) in $slots" #[slotName]="slotProps">
            <slot :name="slotName" v-bind="slotProps ?? {}" />
        </template>
    </Menu>
</template>

<script setup lang="ts">
import Menu, { type MenuPassThroughOptions, type MenuProps } from 'primevue/menu';
import { ref } from 'vue';
import { Icon } from '@iconify/vue';
import { ptViewMerge } from './utils';

interface Props extends /* @vue-ignore */ MenuProps {}
defineProps<Props>();

const theme = ref<MenuPassThroughOptions>({
    root: `bg-neutral-800
        text-neutral-200 dark:text-surface-0
        rounded-md border-1 border-neutral-700
        p-popup:shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1),0_2px_4px_-2px_rgba(0,0,0,0.1)]`,
    list: `m-0 p-1 list-none outline-none flex flex-col`,
    item: `p-disabled:opacity-60 p-disabled:pointer-events-none text-sm`,
    itemContent: `group transition-colors duration-200 rounded-sm text-neutral-200
        hover:bg-neutral-700 hover:text-neutral-2000`,
    itemLink: `cursor-pointer flex items-center no-underline overflow-hidden relative text-inherit
        px-3 py-2 gap-3 select-none outline-none`,
    itemLabel: ``,
    submenuLabel: `opacity-70 text-xs px-3 py-2 text-surface-500 dark:text-surface-400`,
    separator: `border-t border-surface-200 dark:border-surface-700`,
    transition: {
        enterFromClass: 'opacity-0 scale-y-75',
        enterActiveClass: 'transition duration-120 ease-[cubic-bezier(0,0,0.2,1)]',
        leaveActiveClass: 'transition-opacity duration-100 ease-linear',
        leaveToClass: 'opacity-0'
    }
});

const el = ref();
defineExpose({
    // @ts-ignore
    toggle: (event) => el.value.toggle(event)
});
</script>
