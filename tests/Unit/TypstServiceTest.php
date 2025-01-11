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

    expect($document)->toBe("#heading(level: 1)[*Hello* _World_!]\n#par[Content]");
});
