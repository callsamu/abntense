<?php

use App\Models\Document;
use App\Models\User;
use \Illuminate\Foundation\Testing\RefreshDatabase;


use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\actingAs;


pest()->use(RefreshDatabase::class);


beforeEach(function() {
    $this->user = User::factory()->create();
    $this->doc = Document::factory()->create();
    $this->doc->users()->attach($this->user->id, ['role' => Document::ROLE_OWNER]);
});

/*
//test('generates pdf', function () {
//    $response = actingAs($this->user)->get('/documents/compile/' . $this->doc->id);
//    $response->assertStatus(200);
//    $response->assertHeader('Content-Type', 'application/pdf');
});
*/

test('saves document', function () {
    $meta = $this->doc->metadata;
    $meta['name'] = 'new name';
    $meta['description'] = 'new description';
    $title = 'foobar';
    $content = [];
    '/documents/' . $this->doc->id . '/references',

    $route = route(
        'document.update',
        ['id' => $this->doc->id]
    );

    $response = actingAs($this->user)->patchJson($route, [
        'title' =>  $title,
        'metadata' => $meta,
        'content' => $content,
    ]);
    $response->assertStatus(200);

    $this->assertDatabaseHas('documents', [
        'title' => $title,
        'metadata' => json_encode($meta),
        'content' => json_encode($content),
    ]);
});

describe('addReference', function () {
    it('saves reference', function () {
        $reference = [
            'author' => 'John Doe',
            'title' => 'Foo',
            'visited' => date('Y-m-d'),
            'date' => '2000-01-01'
        ];

        $route = route(
            'document.update',
            ['id' => $this->doc->id]
        );

        $response = actingAs($this->user)->postJson(
            $route,
            [
                'type' => 'web',
                'reference' => $reference
            ],
        );

        $response->assertStatus(200);
        $data = json_decode($response->content(), true);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('id', '1')
            ->has('reference', fn (AssertableJson $json) => $json
                ->where('type', 'web')
                ->where('date', $reference['date'])
                ->etc())
        );

        $this->assertDatabaseHas('documents', [
            'references' => json_encode([
                $data['id'] => $data['reference']
            ])
        ]);
    });
});
