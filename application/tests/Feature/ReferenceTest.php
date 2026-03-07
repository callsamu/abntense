<?php

use App\Services\WebsiteMetadataExtractor;
use App\Exceptions\WebsiteMetadataException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->actingAs($user = \App\Models\User::factory()->create());
});

/*
describe('ReferenceController::from_website', function () {

    it('returns metadata for valid URL', function () {
        $html = <<<EOD
            <html lang="en">
                <head>
                    <title>Article Title</title>
                    <meta name="author" content="John Doe">
                    <meta name="date" content="2026-02-22 17:22:52">
                </head>
            </html>
        EOD;

        Http::fake([
            '*' => Http::response(
               $html,
               200,
                ['Content-Type' => 'text/html']
            ),
        ]);

        $response = $this->getJson(route(
            'references.from_website',
            ['url' => 'https://example.org'],
        ));

        $response->assertOk()
            ->assertJsonStructure(['title', 'author', 'date', 'accessed', 'url'])
            ->assertJson([
                'title' => 'Article Title',
                'date' => '2026-02-22',
                'author' => [
                    [
                        'given' => 'John',
                        'family' => 'Doe'
                    ]
                ]
            ]);
    });
});
*/
