import { type Editor, type Range } from '@tiptap/core';
import type { ReferenceElement } from 'reka-ui';

export interface MenuItem {
    id: number;
    key: string;
    label: string;
    icon?: string;
    command: (editor: Editor, range: Range) => boolean;
}

export interface AnchorContext {
    referenceElement: ReferenceElement;
    commandCallback: (props: any) => void;
};




