import Placeholder from '@tiptap/extension-placeholder';
import Details, { DetailsContent, DetailsSummary } from '@tiptap/extension-details';
import Document from '@tiptap/extension-document';
import Paragraph from '@tiptap/extension-paragraph';
import Heading from '@tiptap/extension-heading';
import Text from '@tiptap/extension-text';
import Footnote from './Footnote';
import { TrailingNode } from '@tiptap/extensions';
import { SlashCommandsExtension } from './SlashCommands';
import Bold from '@tiptap/extension-bold';
import Italic from '@tiptap/extension-italic';
import Underline from '@tiptap/extension-underline';

interface EditorExtensionsOpts {
    placeholderClass: string;
}

const Filler = Paragraph.extend({
    name: 'filler',

    addOptions() {
        return {
            HTMLAttributes: {},
        }
    },

    parseHTML() {
        return [
            {
                tag: 'span',
                getAttrs: (node) => {
                  return {
                    class: node.getAttribute('filler'),
                  }
                }
            }
        ]
    },

    renderHTML({ HTMLAttributes }) {
        HTMLAttributes['class'] = 'filler';
        return ['span', HTMLAttributes, 0]
    }
});


declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        pretextual_element: {
            insertPretextualElement: (name: string, desc: string) => ReturnType
        }
    }
}

const PretextualElement = Details.extend({
    name: 'pretextual_element',
    group: 'pretextual',

    addCommands() {
        return {
            insertPretextualElement(name: string, desc: string) {
                return ({ editor, commands }) => {
                    const $elements = editor.$doc.querySelectorAll('pretextual_element');
                    const $element = $elements[$elements.length - 1];

                    const msg = `<details><summary>${name}</summary><p>${desc}</p></details>`;
                    const idx = $element ? $element.pos + $element.size  : 1;

                    return commands.insertContentAt(idx, msg, {updateSelection: true})
                }
            }
        }
    }
});

const ABNTDocument = Document.extend({
    content: 'filler pretextual* block*'
});


const setupEditorExtensions = (opts: EditorExtensionsOpts) => ([
    ABNTDocument,
    Paragraph,
    Heading,
    Text,
    Footnote,
    TrailingNode.configure({
        node: 'paragraph',
    }),
    Placeholder.configure({
        emptyEditorClass: opts.placeholderClass,
        placeholder: "Escreva algo...",
    }),
    PretextualElement.configure({
        persist: true,
        HTMLAttributes: {
            class: 'details',
        }
    }),
    DetailsContent,
    DetailsSummary,
    Filler.configure({
        HTMLAttributes: {
            class: 'filler'
        }
    }),
    SlashCommandsExtension,
    Bold,
    Italic,
    Underline
]);

export default setupEditorExtensions;
