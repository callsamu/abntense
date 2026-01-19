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
import Mention from '@tiptap/extension-mention';
import { BulletList, ListItem, OrderedList } from '@tiptap/extension-list';


declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        pretextual_element: {
            insertPretextualElement: (name: string, desc: string) => ReturnType
        }
        mention: {
            insertCitation: (id: string, label: string) => ReturnType
        }
    }
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
                tag: 'p',
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
        return ['p', HTMLAttributes, 0]
    }
});

const PretextualElement = Details.extend({
    name: 'pretextual_element',
    group: 'pretextual',

    addCommands() {
        return {
            ...(this.parent ? this.parent(): {}),
            insertPretextualElement(name: string, desc: string) {
                return ({ editor, commands }) => {
                    const $elements = editor.$doc.querySelectorAll('pretextual_element');
                    const $element = $elements[$elements.length - 1];

                    const msg = `<details><summary>${name}</summary><p>${desc}</p></details>`;
                    const idx = $element ? $element.pos + $element.size  : 1;

                    return commands.insertContentAt(idx, msg, {updateSelection: true})
                }
            },
        }
    }
});

const PretextualTitle = DetailsSummary.extend({
    onUpdate({ editor }) {
        if (editor.isActive('pretextual_element')) {
            const node = editor.state.selection.$from.parent;
            if (node.type.name == this.name && node.childCount == 0) {
                editor.commands.deleteNode(PretextualElement.name);
            }
        }
    },
}).configure({
    HTMLAttributes: {readonly: true}
});

const PretextualContents = DetailsContent;

const Citation = Mention.extend({
    addCommands() {
        return {
            ...(this.parent ? this.parent(): {}),
            insertCitation(id, label) {
                return ({ commands }) => {
                    const html = `<span class="citation" data-type="mention" data-id="${id}" data-label="${label}"></span>`;
            		return commands.insertContent(html);
                }
            },
        }
    }
}).configure({
    HTMLAttributes: {
        class: 'citation'
    },
    deleteTriggerWithBackspace: true,
});

const ABNTDocument = Document.extend({
    content: 'filler pretextual* block*'
});

const setupEditorExtensions = (opts: {
    placeholderClass: string
}) => ([
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
    PretextualTitle,
    PretextualContents,
    Filler.configure({
        HTMLAttributes: {
            class: 'filler'
        }
    }),
    Citation,
    SlashCommandsExtension,
    Bold,
    Italic,
    Underline,
    BulletList,
    OrderedList,
    ListItem
]);

export default setupEditorExtensions;
