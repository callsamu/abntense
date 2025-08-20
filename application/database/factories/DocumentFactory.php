<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'metadata' => [
                'location' => $this->faker->word(),
                'description' => $this->faker->text(64),
                'institution' => $this->faker->company(),
                'year' => $this->faker->year(),
            ],
            'content' => [
                "type" => "doc",
                "content" => [
                    [
                        "type" => "heading",
                        "attrs" => ["level" => 1],
                        "content" => [
                            [
                                "type" => "text",
                                "text" => "Resumo",
                            ]
                        ]
                    ],
                    [
                        "type" => "paragraph",
                        "content" => [
                            [
                                "type" => "text",
                                "text" => "Esse é um componente obrigatório e deve ser feito em um único parágrafo contendo de 150 a 500 palavras. É necessário ainda que o texto esteja na terceira pessoa do singular e em voz ativa."
                            ]
                        ]
                    ],
                    [
                        "type" => "heading",
                        "attrs" => ["level" => 1],
                        "content" => [
                            [
                                "type" => "text",
                                "text" => "Introdução",
                            ]
                        ]
                    ],
                    [
                        "type" => "paragraph",
                        "content" => [
                            [
                                "type" => "text",
                                "text" => $this->faker->text(256),
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
