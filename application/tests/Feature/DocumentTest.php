<?php

use App\Models\Document;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('generates pdf', function () {
    $response = $this->get('/');

    $user = User::factory()->create();
    $doc = Document::factory()->create();
    $doc->users()->attach($user->id, ['role' => Document::ROLE_OWNER]);

    $response = actingAs($user)->get('/documents/compile/' . $doc->id);
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
});

test('saves document', function () {
    $response = $this->get('/');

    $user = User::factory()->create();
    $doc = Document::factory()->create();
    $doc->users()->attach($user->id, ['role' => Document::ROLE_OWNER]);
    $meta = $doc->metadata;

    $meta['name'] = 'new name';
    $meta['description'] = 'new description';

    $response = actingAs($user)->patchJson('/documents/' . $doc->id, [
        'metadata' => $meta,
        'content' => []
    ]);
    $response->assertStatus(200);

    $doc = Document::find($doc->id);

    $this->assertEquals($meta['name'], $doc->first()->metadata['name']);
    $this->assertEquals($meta['description'], $doc->first()->metadata['description']);
    $this->assertEquals([], $doc->content);


});


