import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';
import Details, { DetailsContent, DetailsSummary } from '@tiptap/extension-details';

interface EditorExtensionsOpts {
    placeholderClass: string;
}

const setupEditorExtensions = (opts: EditorExtensionsOpts) => ([
    Placeholder.configure({
        emptyEditorClass: opts.placeholderClass,
        placeholder: "Escreva algo...",
    }),
    StarterKit.configure({
        horizontalRule: false,
        codeBlock: false,
        code: false,
    }),
    Details.configure({
        persist: true,
        HTMLAttributes: {
            class: 'details',
        }
    }),
    DetailsContent,
    DetailsSummary,
]);

export default setupEditorExtensions;
