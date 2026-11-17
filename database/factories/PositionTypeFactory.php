<?php

namespace Database\Factories;

use App\Models\PositionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PositionType>
 */
class PositionTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category' => fake()->randomElement([
                'Acara & Operasional', 'Registrasi', 'Publikasi & Dokumentasi',
                'Hubungan & Komunikasi', 'Logistik',
            ]),
            'default_description' => fake('id_ID')->sentence(12),
            'default_requirements' => implode("\n", fake('id_ID')->sentences(3)),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
