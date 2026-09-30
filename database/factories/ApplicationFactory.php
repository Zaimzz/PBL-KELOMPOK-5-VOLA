<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\EventPosition;
use App\Models\VolunteerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'volunteer_profile_id' => VolunteerProfile::factory(),
            'position_id' => EventPosition::factory(),
            'status' => ApplicationStatus::Pending,
            'cover_letter' => fake('id_ID')->paragraph(2),
            'applied_at' => now(),
            'decided_at' => null,
            'decision_note' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ApplicationStatus::Accepted,
            'decided_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ApplicationStatus::Rejected,
            'decided_at' => now(),
            'decision_note' => 'Kuota posisi sudah terpenuhi.',
        ]);
    }
}
