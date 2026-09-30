<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\OrganizerProfile;
use App\Models\Payment;
use App\Models\VolunteerProfile;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $volunteers = VolunteerProfile::factory()->count(10)->create();

        $eoVerified = OrganizerProfile::factory()->verified()->count(6)->create();
        $eoPending = OrganizerProfile::factory()->count(3)->create();
        $eoRejected = OrganizerProfile::factory()->rejected()->count(1)->create();
        $allEo = $eoVerified->concat($eoPending)->concat($eoRejected);

        $events = collect();

        $events = $events->concat(
            Event::factory()->count(5)->active()->create([
                'organizer_id' => fn () => $eoVerified->random()->id,
            ])
        );

        $events = $events->concat(
            Event::factory()->count(4)->pendingReview()->create([
                'organizer_id' => fn () => $eoVerified->random()->id,
            ])
        );

        $events = $events->concat(
            Event::factory()->count(3)->create([ // draft
                'organizer_id' => fn () => $eoVerified->random()->id,
            ])
        );

        $events = $events->concat(
            Event::factory()->count(2)->rejected()->create([
                'organizer_id' => fn () => $eoVerified->random()->id,
            ])
        );

        $events = $events->concat(
            Event::factory()->count(1)->finished()->create([
                'organizer_id' => fn () => $eoVerified->random()->id,
            ])
        );

        $positions = collect();
        foreach ($events as $event) {
            $positions = $positions->concat(
                EventPosition::factory()
                    ->count(fake()->numberBetween(2, 3))
                    ->create(['event_id' => $event->id])
            );
        }

        $used = [];
        $created = 0;
        $targetApplications = 25;
        $attempts = 0;

        while ($created < $targetApplications && $attempts < 200) {
            $attempts++;
            $volunteer = $volunteers->random();
            $position = $positions->random();
            $key = $volunteer->id.'-'.$position->id;

            if (in_array($key, $used)) {
                continue;
            }
            $used[] = $key;

            $status = fake()->randomElement(['pending', 'pending', 'accepted', 'rejected']);

            $application = Application::factory()->create([
                'volunteer_profile_id' => $volunteer->id,
                'position_id' => $position->id,
            ]);

            if ($status === 'accepted') {
                $application->update([
                    'status' => 'accepted',
                    'decided_at' => now(),
                ]);
            } elseif ($status === 'rejected') {
                $application->update([
                    'status' => 'rejected',
                    'decided_at' => now(),
                    'decision_note' => 'Kuota posisi sudah terpenuhi.',
                ]);
            }

            $created++;
        }

        foreach ($events->whereIn('status', ['active']) as $event) {
            Payment::factory()->paid()->create(['event_id' => $event->id]);
        }

        foreach ($events->whereIn('status', ['pending_review']) as $event) {
            Payment::factory()->create(['event_id' => $event->id]); // pending
        }
    }
}
