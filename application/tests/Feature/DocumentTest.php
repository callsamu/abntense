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
    $user = User::factory()->create();
    $doc = Document::factory()->create();
    $doc->users()->attach($user->id, ['role' => Document::ROLE_OWNER]);

    $meta = $doc->metadata;
    $meta['name'] = 'new name';
    $meta['description'] = 'new description';
    $title = 'foobar';
    $content = [];

    $response = actingAs($user)->patchJson('/documents/' . $doc->id, [
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
