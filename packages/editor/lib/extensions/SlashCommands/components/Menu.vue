<script setup lang="ts">
import { provide, ref, type Ref, computed, useTemplateRef, watch } from 'vue';
import { queryInjectionKey, anchorInjectionKey, rangeInjectionKey } from './keys';
import type { AnchorContext, MenuItem } from './types';
import { Icon } from '@iconify/vue';
import {
    ComboboxRoot,
    ComboboxEmpty,
    ComboboxInput,
    ComboboxViewport,
    ComboboxContent,
    ComboboxItem,
    useFilter,
} from 'reka-ui';

import { type Editor, type Range } from '@tiptap/core';

const props = defineProps<{
    editor: Editor,
    items: MenuItem[]
}>();

const query = ref('');
const range: Ref<Range | null> = ref(null);
const anchor: Ref<AnchorContext | null> = ref(null);

function updateQuery(q: string) {
    query.value = q;
}

function updateAnchor(p: AnchorContext | null) {
    anchor.value = p;
}

function updateRange(r: Range | null) {
    range.value = r;
}

provide(queryInjectionKey, { value: query, updateValue: updateQuery });
provide(anchorInjectionKey, { value: anchor, updateValue: updateAnchor });
provide(rangeInjectionKey, { value: range, updateValue: updateRange});

const root = useTemplateRef('root');

const { startsWith } = useFilter({ sensitivity: 'base' })
const filteredItems = computed(() => props.items.filter(i => startsWith(i.key, query.value)))

const itemIdx = ref(0);

watch(query, () => {
    if (!startsWith(query.value, filteredItems.value[itemIdx.value]?.key ?? "")) {
        itemIdx.value = 0;

        if (root.value?.highlightFirstItem) {
            root.value?.highlightFirstItem();
        }
    }
});

watch(itemIdx, () => {
    const item = filteredItems.value[itemIdx.value];

    if (item && root.value?.highlightItem) {
        root.value?.highlightItem(item.id);
    }
})

function handleSelect(item: MenuItem) {
    if (anchor.value && range.value) {
        anchor.value.commandCallback({
            command: () => item.command(props.editor, range.value as Range)
        });
    }
}

function handleKey(key: string) {
    const len = filteredItems.value.length;

    switch (key) {
        case 'ArrowDown':
            itemIdx.value = (itemIdx.value + 1) % len;
            break;
        case 'ArrowUp':
            itemIdx.value = itemIdx.value <= 0 ? len - 1 : itemIdx.value - 1;
            break;
        default:
    }
}
</script>

<template>
        <slot></slot>

        <ComboboxRoot
            ref="root"
            ignore-filter
            :open="anchor !== null"
            @keydown="$event.stopPropagation()"
        >
           <ComboboxInput :style="{ display: 'none' }" v-model="query" />

            <ComboboxContent
                v-if="anchor"
                :reference="anchor.referenceElement"
                position='popper'
                positionStrategy="absolute"
                align="start"
                side='bottom'
                :sideOffset="10"
                @keydown="handleKey($event.key)"
                class="absolute z-10 rounded border border-neutral-700 bg-neutral-800 shadow-md min-w-32 w-38 text-neutral-200 text-sm"
            >
                <div id="slash-command-menu"></div>
                <ComboboxViewport class="shadow-md flex flex-col">
                    <ComboboxEmpty class="p-1 py-2 pl-2 text-italic">
                        Vazio
                    </ComboboxEmpty>
                    <ComboboxItem
                       v-for="(item, idx) in filteredItems"
                       :key="item.id"
                       :value="item.id"
                       @mouseover="itemIdx = idx"
                       @select="handleSelect(item)"
                       class="text-xs leading-none flex items-center
                        p-1 py-2 pl-2 relative select-none gap-2
                        data-[disabled]:pointer-events-none data-[highlighted]:outline-none
                        data-[highlighted]:bg-neutral-700 data-[highlighted]:text-neutral-200"
                    >
                        <Icon v-if ="item.icon":icon="item.icon" />
                        <span> {{ item.label }} </span>
                    </ComboboxItem>
                </ComboboxViewport>
            </ComboboxContent>
        </ComboboxRoot>
</template>



