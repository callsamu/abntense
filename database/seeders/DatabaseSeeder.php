<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()
            ->hasAttached(
                Document::factory(3),
                ['role' => Document::ROLE_OWNER]
            )->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        Log::info($user);
    }
}
