import { useEditor as _useEditor, type JSONContent } from '@tiptap/vue-3';
import { enableKeyboardNavigation } from './extensions/SlashCommands';
import setupEditorExtensions from './extensions';

interface EditorOpts {
    debug?: boolean;
    initialContent: JSONContent | null;
}

export default function useEditor(opts: Partial<EditorOpts>) {
    return _useEditor({
        content: opts.initialContent ?? `<p class="filler"></p><p></p>`,
        extensions: setupEditorExtensions({
           placeholderClass: '.empty-node',
        }),
        enableContentCheck: true,
        onUpdate: ({ editor }) => {
            if (opts.debug) console.debug(editor.getJSON())
        },
        editorProps: {
             handleDOMEvents : {
                keydown: (_, v) => enableKeyboardNavigation(v),
            }
        }
    });
}
