<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['name' => 'Pop', 'color' => '#E11D48'],
            ['name' => 'Indie', 'color' => '#7C3AED'],
            ['name' => 'Hip-Hop', 'color' => '#EA580C'],
            ['name' => 'Lo-fi', 'color' => '#0D9488'],
            ['name' => 'Jazz', 'color' => '#1D4ED8'],
            ['name' => 'Rock', 'color' => '#B91C1C'],
            ['name' => 'Electronic', 'color' => '#0891B2'],
            ['name' => 'R&B', 'color' => '#A21CAF'],
            ['name' => 'Dangdut', 'color' => '#CA8A04'],
            ['name' => 'K-Pop', 'color' => '#DB2777'],
            ['name' => 'Acoustic', 'color' => '#15803D'],
            ['name' => 'Classical', 'color' => '#475569'],
        ];

        foreach ($genres as $genre) {
            Category::updateOrCreate(
                ['slug' => Str::slug($genre['name'])],
                ['name' => $genre['name'], 'color' => $genre['color']],
            );
        }
    }
}
