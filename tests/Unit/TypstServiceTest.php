<?php

use App\Services\TypstService;


test('Conversion from Tiptap JSON', function () {
    $typst = new TypstService();
    $document = $typst->fromTiptap([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'heading',
                'attrs' => [
                    'level' => 1
                ],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello',
                        'marks' => [
                            [
                                'type' => 'bold'
                            ]
                        ]
                    ],
                    [
                        'type' => 'text',
                        'text' => ' '
                    ],
                    [
                        'type' => 'text',
                        'text' => 'World',
                        'marks' => [
                            [
                                'type' => 'italic'
                            ]
                        ]
                    ],
                    [
                        'type' => 'text',
                        'text' => '!'
                    ]
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Content'
                    ]
                ]
            ]
        ]
    ]);

    $want = "#heading(level: 1)[*Hello* _World_!]";
    $want .= "\n#par[Content]\n";

    expect($document)->toBe($want);
});

test('Conversion of Pretextual Elements', function () {
    $typst = new TypstService();
    $document = $typst->fromTiptap([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'heading',
                'attrs' => [
                    'level' => 1
                ],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Resumo'
                    ],
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Content'
                    ]
                ]
            ],
            [
                'type' => 'heading',
                'attrs' => [
                    'level' => 1
                ],
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello'
                    ],
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => 'World'
                    ]
                ]
            ],
        ]
    ]);

    $want = "#pretextual(\"Resumo\")[\n#par[Content]\n]\n";
    $want .= "#heading(level: 1)[Hello]\n#par[World]\n";

    expect($document)->toBe($want);
});
