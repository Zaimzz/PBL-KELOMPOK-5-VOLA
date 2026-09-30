<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventPosition>
 */
class EventPositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->randomElement([
                'Liaison Officer', 'Usher & Registrasi', 'Dokumentasi & Media',
                'Divisi Acara & Stage Management', 'Konsumsi & Logistik',
            ]),
            'description' => fake('id_ID')->sentence(10),
            'requirements' => fake('id_ID')->sentence(8),
            'quota' => fake()->numberBetween(3, 15),
        ];
    }
}
