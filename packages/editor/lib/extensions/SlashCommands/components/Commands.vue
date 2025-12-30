<script setup lang="ts">
import { inject, Teleport } from 'vue';
import { popoverInjectionKey } from './keys';
import { ComboboxRoot } from 'reka-ui';

const popover = inject(popoverInjectionKey);
</script>

<template>
    <Teleport v-if="popover?.value":to="popover?.value">

    </Teleport>
</template>
