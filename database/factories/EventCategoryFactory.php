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
        static $usedNames = [];
        $allNames = ['Festival', 'Seminar', 'Sosial', 'Kampus', 'Olahraga', 'Musik', 'Komunitas'];
        $available = array_diff($allNames, $usedNames);

        if (empty($available)) {
            // All base names used, generate unique variant
            $name = fake()->unique()->word().' Event';
        } else {
            $name = fake()->randomElement($available);
            $usedNames[] = $name;
        }

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->randomNumber(4),
        ];
    }
}
