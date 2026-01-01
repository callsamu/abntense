import { useEditor as _useEditor, type JSONContent } from '@tiptap/vue-3';
import { enableKeyboardNavigation } from './extensions/SlashCommands';
import setupEditorExtensions from './extensions';

interface EditorOpts {
    initialContent: JSONContent | null;
}

export default function useEditor(opts: Partial<EditorOpts>) {
    return _useEditor({
        content: opts.initialContent ?? `<span class="filler"></span><p></p>`,
        extensions: setupEditorExtensions({
           placeholderClass: '.empty-node',
        }),
        enableContentCheck: true,

        editorProps: {
             handleDOMEvents : {
                keydown: (_, v) => enableKeyboardNavigation(v),
            }
        }
    });
}
