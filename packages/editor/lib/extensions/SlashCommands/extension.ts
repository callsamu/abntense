import Anchor from './components/Anchor.vue';
import { Extension, VueRenderer } from '@tiptap/vue-3'
import { Suggestion, type SuggestionKeyDownProps,  type SuggestionOptions,  type SuggestionProps } from '@tiptap/suggestion';
import type { MenuItem } from './components/types';


function render() {
    let component: VueRenderer;

    return {
        onStart(props: SuggestionProps) {
            component = new VueRenderer(Anchor, {
                props: {...props},
                editor: props.editor

            })

            if (!props.clientRect) {
                return;
            }

            if (component.element) {
                document.body.appendChild(component.element);
            }
        },

        onUpdate(props: SuggestionProps) {
            component.updateProps(props)

            if (!props.clientRect) {
              return
            }
        },

        onKeyDown(props: SuggestionKeyDownProps) {
            console.log(props.event.key);
            if (props.event.key === 'Escape') {
              component.destroy()

              return true
            }

            return component.ref?.onKeyDown(props.event);
        },

        onExit() {
            component?.element?.remove()
            component.destroy()
        },
    }
}

export function generateMenuItems(items: Omit<MenuItem, 'id'>[]): MenuItem[] {
    return items.map((item, idx) => ({
        id: idx,
        ...item,
    }));
}

const navigationKeys = ["ArrowUp", "ArrowDown", "Enter"];

export function enableKeyboardNavigation(event: KeyboardEvent) {
  if (navigationKeys.includes(event.key)) {
    const slashCommand = document.querySelector("#slash-command-menu");

    if (slashCommand) {
    }
  }
}

export const SlashCommandsExtension = Extension.create({
  name: "slash-commands",

  addOptions() {
    return {
      suggestion: {
        char: "/",
        command: ({ props }) => {
            props.command();
        },
        render: render,
      } satisfies Partial<SuggestionOptions>,
    };
  },

  addProseMirrorPlugins() {
    return [
      Suggestion({
        editor: this.editor,
        ...this.options.suggestion,
      }),
    ];
  },
});



