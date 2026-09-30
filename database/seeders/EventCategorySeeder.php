<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Festival', 'Seminar', 'Sosial', 'Kampus',
            'Olahraga', 'Musik', 'Komunitas',
        ];

        foreach ($categories as $name) {
            EventCategory::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

    }
}
