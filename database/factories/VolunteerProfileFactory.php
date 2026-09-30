<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VolunteerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VolunteerProfile>
 */
class VolunteerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->volunteer(),
            'bio' => fake('id_ID')->sentence(15),
            'institution' => fake()->boolean(70)
                ? fake('id_ID')->randomElement([
                    'Politeknik Negeri Malang', 'Universitas Brawijaya',
                    'Universitas Negeri Malang', 'Universitas Muhammadiyah Malang',
                ])
                : null,
            'city' => fake('id_ID')->city(),
            'skills' => fake()->randomElements(
                ['Public Speaking', 'Fotografi', 'Desain Grafis', 'Videografi',
                    'MC', 'Bahasa Inggris', 'Manajemen Waktu', 'Kepemimpinan'],
                fake()->numberBetween(2, 4)
            ),
            'experience' => fake('id_ID')->paragraph(2),
            'cv_path' => null,
            'portfolio_path' => null,
        ];
    }
}
