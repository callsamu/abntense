<?php

use App\Services\TypstConversor;



test('Conversion from Tiptap JSON', function () {
    $typst = new TypstConversor();
    $document = $typst->convertTiptap([
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

    $want = "#heading(level: 1)[*Hello* _World_!]\n";
    $want .= "#par[Content]\n";

    expect($document)->toBe($want);
});

test('Conversion of Pretextual Elements', function () {
    $typst = new TypstConversor();
    $document = $typst->convertTiptap([
        "type" => "doc",
        "content" => [
            [
              "type" => "filler"
            ],
            [
                'type' => 'pretextual_element',
                'content' => [
                    [
                        'type' => 'detailsSummary',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Resumo'
                            ]
                        ]
                    ],
                    [
                        'type' => 'detailsContent',
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    [
                                        'type' => 'text',
                                        'text' => 'Esse é um componente obrigatório e deve ser feito em um único parágrafo contendo de 150 a 500 palavras. É necessário ainda que o texto esteja na terceira pessoa do singular e em voz ativa.'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ],
    ]);

    dd($document);
});
