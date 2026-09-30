<?php

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\OrganizerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizerProfile>
 */
class OrganizerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->eo(),
            'organization_name' => fake('id_ID')->company(),
            'organization_type' => fake()->randomElement(['Komunitas', 'Perusahaan', 'Yayasan', 'Kampus']),
            'description' => fake('id_ID')->paragraph(3),
            'city' => fake('id_ID')->city(),
            'pic_name' => fake('id_ID')->name(),
            'pic_phone' => fake('id_ID')->phoneNumber(),
            'social_link' => fake()->url(),
            'document_path' => null,
            'verification_status' => VerificationStatus::Pending,
            'verified_by' => null,
            'verified_at' => null,
            'rejection_reason' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::Verified,
            'verified_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => VerificationStatus::Rejected,
            'rejection_reason' => 'Dokumen legalitas tidak lengkap.',
        ]);
    }
}
