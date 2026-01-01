import type { InjectionKey, Ref } from "vue";
import type { Range } from '@tiptap/core';
import type { AnchorContext } from "./types";

type ReactiveInjectionKey<T> = InjectionKey<{
    value: Ref<T>,
    updateValue: (val: T) => void,
}>

export const queryInjectionKey = Symbol() as ReactiveInjectionKey<string>;
export const anchorInjectionKey = Symbol() as ReactiveInjectionKey<AnchorContext | null>;
export const rangeInjectionKey = Symbol() as ReactiveInjectionKey<Range | null>;
