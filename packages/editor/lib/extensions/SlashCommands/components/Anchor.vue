<script setup lang="ts">
import { inject, onMounted, onUnmounted, watch } from 'vue';
import { anchorInjectionKey, queryInjectionKey, rangeInjectionKey } from './keys';
import type { SuggestionProps } from '@tiptap/suggestion';

const query = inject(queryInjectionKey);
const anchor = inject(anchorInjectionKey);
const range = inject(rangeInjectionKey);

const props = defineProps<SuggestionProps>();

onMounted(() => {
    range?.updateValue(props.range);
    query?.updateValue(props.query);
    anchor?.updateValue({
        commandCallback: props.command,
        referenceElement: {
            getBoundingClientRect: props.clientRect as () => DOMRect,
        },
    });
});

onUnmounted(() => {
    anchor?.updateValue(null);
});

watch(
    () => props.query,
    async(newQuery) => {
        query?.updateValue(newQuery);
    }
);

watch(
    () => props.range,
    async(newRange) => {
        range?.updateValue(newRange);
    }
);

const navigationKeys = ["ArrowUp", "ArrowDown", "Enter"];

function onKeyDown(event:KeyboardEvent) {
    if (navigationKeys.includes(event.key)) {
        const commandRef = document.querySelector("#slash-command-menu");

        event.preventDefault();

        if (commandRef) {
            commandRef.dispatchEvent(
                new KeyboardEvent("keydown", {
                    key: event.key,
                    cancelable: true,
                    bubbles: true,
                }),
            );
        }

        return true;
    }

    return false;
}

defineExpose({ onKeyDown });

</script>

<template>
</template>
