<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Fighter;
use App\Models\Thread;
use App\Models\Post;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Event::factory(5)
            ->has(Thread::factory(3)
                ->has(Post::factory(10)))
            ->create();

        Fighter::factory(10)
            ->has(Thread::factory(2)
                ->has(Post::factory(5)))
            ->create();

    }
}
