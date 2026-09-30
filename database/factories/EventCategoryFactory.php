<?php

namespace Database\Factories;

use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventCategory>
 */
class EventCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Festival', 'Seminar', 'Sosial', 'Kampus', 'Olahraga', 'Musik', 'Komunitas',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
