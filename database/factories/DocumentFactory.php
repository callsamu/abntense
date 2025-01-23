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
                        "type" => "paragraph",
                        "content" => [
                            [
                                "type" => "text",
                                "text" => $this->faker->text(256)
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
