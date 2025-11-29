import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';

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
]);

export default setupEditorExtensions;





