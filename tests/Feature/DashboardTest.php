<?php

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Inertia\Testing\AssertableInertia as Assert;

const DOCUMENT_COUNT = 3;

test('example', function () {
    $users = User::factory()
        ->count(1 + DOCUMENT_COUNT)
        ->create();

    $user = $users->first();
    $collaborators = $users->slice(1, DOCUMENT_COUNT);

    $descrescentTimestamps = fn (Sequence $sequence) =>
        ['updated_at' => now()->subDays($sequence->index)];

    $documents = Document::factory()
        ->count(DOCUMENT_COUNT)
        ->sequence($descrescentTimestamps)
        ->create();

    assertEquals(DOCUMENT_COUNT, $documents->count());
    assertTrue($documents->count() == $collaborators->count());

    $documents->zip($collaborators)->each(function ($item) use ($user) {
        [$document, $collaborator] = $item;
        $document->users()->attach([
            $user->id => ['role' => Document::ROLE_OWNER],
            $collaborator->id => ['role' => Document::ROLE_EDITOR]
        ]);
    });

    $response = actingAs($user)
        ->get('/dashboard');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('documents', DOCUMENT_COUNT, fn (Assert $page) => $page
            ->where('title', $documents->first()->title)
            ->where('users', [
                ['name' => $user->name],
                ['name' => $collaborators->first()->name],
            ])->etc()
        )
    );

    $response->assertStatus(200);
});
