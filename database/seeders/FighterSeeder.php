<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fighter;

class FighterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fighter::create([
            'name' => '那須川天心',
            'image_url' => 'fighters/nasukawa_tenshin.jpg',
        ]);

        Fighter::create([
            'name' => '武尊',
            'image_url' => 'fighters/takeru.jpg',
        ]);

        Fighter::create([
            'name' => '井上尚弥',
            'image_url' => 'fighters/inoue_naoya.jpg',
        ]);

        Fighter::create([
            'name' => '朝倉未来',
            'image_url' => 'fighters/asakura_mikuru.jpg',
        ]);
    }
}
