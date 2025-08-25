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
    $user = User::factory()->create();

    $descrescentTimestamps = fn (Sequence $sequence) =>
        ['updated_at' => now()->subDays($sequence->index)];

    $documents = Document::factory()
        ->count(DOCUMENT_COUNT)
        ->sequence($descrescentTimestamps)
        ->create();

    assertEquals(DOCUMENT_COUNT, $documents->count());

    $documents->each(function ($item) use ($user) {
        $document = $item;
        $document->users()->attach([
            $user->id => ['role' => Document::ROLE_OWNER],
        ]);
    });

    $response = actingAs($user)
        ->get('/dashboard');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('documents', DOCUMENT_COUNT, fn (Assert $page) => $page
            ->where('title', $documents->first()->title)
            ->where('users',
                [$user->name],
            )->etc()
        )
    );

    $response->assertStatus(200);
});
