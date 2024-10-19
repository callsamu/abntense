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
        $content = [
            "type" => "doc",
            "content" => [
                [
                    "type" => "paragraph",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => $this->faker->paragraph(2)
                        ]
                    ]
                ]
            ]
        ];

        return [
            'title' => $this->faker->sentence(3),
            'metadata' => [
                'local' => $this->faker->word(),
                'description' => $this->faker->paragraph(3),
            ],
            'content' => [
                "type" => "doc",
                "content" => [
                    [
                        "type" => "paragraph",
                        "content" => [
                            [
                                "type" => "text",
                                "text" => $this->faker->paragraph(2)
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
