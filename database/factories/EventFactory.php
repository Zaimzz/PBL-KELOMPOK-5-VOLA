<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\OrganizerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake('id_ID')->randomElement([
            'Festival Musik', 'Seminar Nasional', 'Konferensi Teknologi',
            'Bakti Sosial', 'Turnamen Olahraga', 'Pameran Kreatif',
            'Workshop Kewirausahaan', 'Malam Amal',
        ]).' '.fake('id_ID')->city().' '.fake()->year();

        $startDate = fake()->dateTimeBetween('+1 week', '+2 months');

        return [
            'organizer_id' => OrganizerProfile::factory()->verified(),
            'category_id' => EventCategory::inRandomOrder()->first()?->id
                ?? EventCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(4),
            'description' => fake('id_ID')->paragraphs(3, true),
            'poster_path' => null,
            'location' => fake('id_ID')->streetAddress(),
            'city' => fake('id_ID')->city(),
            'start_date' => $startDate,
            'end_date' => (clone $startDate)->modify('+2 days'),
            'start_time' => '08:00',
            'end_time' => '17:00',
            'registration_deadline' => (clone $startDate)->modify('-3 days'),
            'contact_info' => fake('id_ID')->phoneNumber(),
            'coordination_link' => null,
            'pic_name' => fake('id_ID')->name(),
            'pic_phone' => fake('id_ID')->phoneNumber(),
            'benefits' => ['Uang saku', 'Konsumsi', 'E-sertifikat'],
            'status' => EventStatus::Draft,
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'published_at' => null,
        ];
    }

    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::PendingReview,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::Active,
            'published_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::Rejected,
            'rejection_reason' => 'Informasi event kurang lengkap.',
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::Finished,
            'published_at' => now()->subMonth(),
            'start_date' => now()->subMonth(),
            'end_date' => now()->subMonth()->addDays(2),
            'registration_deadline' => now()->subMonth()->subDays(3),
        ]);
    }
}
