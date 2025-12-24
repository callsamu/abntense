import { Node as TiptapNode } from '@tiptap/core';
import { EditorState, type Transaction } from '@tiptap/pm/state';
import { StepMap } from '@tiptap/pm/transform';
import { EditorView } from '@tiptap/pm/view';
import { arrow, computePosition, offset } from '@floating-ui/dom';
import { undo, redo } from '@tiptap/pm/history';
import { keymap } from '@tiptap/pm/keymap';



declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        footnote: {
            insertFootnote: () => ReturnType
        }
    }
}


export default TiptapNode.create({
    name: 'footnote',
    group: 'inline',
    inline: true,
    content: 'inline*',
    atom: true,

    addOptions() {
        return {
            HTMLAttributes: {}
        }
    },

    parseHTML() {
        return [
            {
                tag: 'footnote'
            }
        ]
    },

    renderHTML({ HTMLAttributes }) {
        return ['footnote', HTMLAttributes, 0];
    },

    addCommands() {
        return {
            insertFootnote: () => ({ commands  }) => {
                return commands.insertContent('<footnote></footnote', {
                    updateSelection: true,
                });
            }
        }
    },

    addNodeView() {
        return ({ editor, getPos, node }) => {
            let innerView: EditorView | null = null;

            const dom = document.createElement('footnote');

            function dispatchInner(tr: Transaction) {
                if (!innerView || !getPos) return;
                let { state, transactions } = innerView.state.applyTransaction(tr);
                innerView.updateState(state);

                if (!tr.getMeta('fromOutside')) {
                    let outerTr = editor.view.state.tr;

                    const pos = getPos();
                    if (!pos) throw new Error("could not get pos");

                    let offsetMap = StepMap.offset(pos + 1);

                    transactions.forEach(tr => {
                        tr.steps.forEach(step => {
                            const mapping = step.map(offsetMap);
                            if (mapping) outerTr.maybeStep(mapping)
                        })
                    })

                    if (outerTr.docChanged) editor.view.dispatch(outerTr);
                }
            }

            function open() {
                const tooltip = dom.appendChild(document.createElement('div'))
                tooltip.className = "footnote-tooltip";

                const arrowEl = tooltip.appendChild(document.createElement('div'));
                arrowEl.id = 'tooltip-arrow';

                const editorEl = tooltip.appendChild(document.createElement('div'));

                innerView = new EditorView(editorEl, {
                    state: EditorState.create({
                        doc: node,
                        plugins: [keymap({
                            "Mod-z": () => undo(editor.view.state, editor.view.dispatch),
                            "Mod-y": () => redo(editor.view.state, editor.view.dispatch),
                        })]
                    }),
                    dispatchTransaction: dispatchInner,
                    handleDOMEvents: {
                        mousedown: () => {
                            if (editor.view.hasFocus()) innerView?.focus()
                        }
                    }
                })

                computePosition(dom, tooltip, {
                    placement: 'bottom-start',
                    middleware: [
                        offset({ crossAxis: -20 }),
                        arrow({ element: arrowEl }),
                    ],
                }).then(({ x, y, middlewareData }) => {
                    Object.assign(tooltip.style, {
                        left: `${x}px`,
                        top: `${y}px`,
                    });

                    if (middlewareData.arrow) {
                        const {x, y} = middlewareData.arrow;
                        Object.assign(arrowEl.style, {
                            left: x != null ? `${x}px` : '',
                            top: y != null ? `${y}px` : '',
                        })
                    }
                });
            }

            function close() {
                if (innerView) {
                    innerView.destroy();
                    innerView = null;
                }

                dom.textContent = "";
            }

            return {
                dom,

                selectNode() {
                    dom.classList.add('ProseMirror-selectednode');
                    if (!innerView) open();
                },

                deselectNode() {
                    if (innerView) close();
                    dom.classList.remove('ProseMirror-selectednode');
                },

                destroy() {
                    if (innerView) close();
                },

                update(_node) {
                    if (!node.sameMarkup(_node)) return false
                    node = _node

                    if (innerView) {
                        let state = innerView.state
                        let start = node.content.findDiffStart(state.doc.content)
                        if (start != null) {
                            let {
                                a: endA,
                                b: endB
                            } = node.content.findDiffEnd(state.doc.content) ?? {a: 0, b: 0};

                            let overlap = start - Math.min(endA, endB)
                            if (overlap > 0) { endA += overlap; endB += overlap }
                            innerView.dispatch(
                                state.tr
                                .replace(start, endB, node.slice(start, endA))
                                .setMeta("fromOutside", true)
                            );
                        }
                    }

                    return true
                },

                stopEvent(event): boolean {
                    const target = event.target;
                    if (!target) throw new Error('no event target');

                    if (!innerView) return false;
                    return innerView.dom.contains(event.target as Node);
                },

                ignoreMutation() { return true },
            }
        };
    },


})

