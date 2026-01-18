<script setup lang="ts">
import { Icon } from '@iconify/vue'
import { X } from 'lucide-vue-next';
import {
    DialogContent,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogClose,
    DialogTitle,
    DialogRootProps,
    DialogRootEmits,
    useForwardPropsEmits,
} from 'reka-ui'

const props = defineProps<DialogRootProps>();
const emits = defineEmits<DialogRootEmits>();

const forwaded = useForwardPropsEmits(props, emits);

</script>

<template>
    <DialogRoot v-bind="forwaded">
      <DialogPortal>
        <DialogOverlay class="bg-black opacity-40 data-[state=open]:animate-overlayShow fixed inset-0 z-30" />
        <DialogContent
          class="
            fixed top-[50%] left-[50%] max-h-[85vh] w-[90vw] max-w-2xl
            translate-x-[-50%] translate-y-[-50%] rounded-[6px] border border-neutral-700 bg-neutral-900
            shadow-[hsl(206_22%_7%_/_35%)_0px_10px_38px_-10px,_hsl(206_22%_7%_/_20%)_0px_10px_20px_-15px] focus:outline-none z-[100]
          "
        >
            <DialogTitle>
                <h1 class="font-bold text-2xl text-neutral-100 m-8">
                    <slot name="title"></slot>
                </h1>
            </DialogTitle>
            <slot name="content"></slot>
            <DialogClose class="absolute top-4 right-4 text-neutral-500 hover:text-neutral-400 cursor-pointer">
                <X />
            </DialogClose>
        </DialogContent>
      </DialogPortal>
    </DialogRoot>
</template>
