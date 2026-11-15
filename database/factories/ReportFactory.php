<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reasons = [
            'Ketidaksesuaian Pembagian Tugas di Lapangan',
            'Keterlambatan Pembayaran Uang Saku & Transportasi',
            'Penyelenggara Tidak Dapat Dihubungi Menjelang Acara',
            'Pelecehan Verbal atau Perilaku Tidak Pantas',
            'Pembatalan Partisipasi Mendadak Tanpa Alasan Jelas',
            'Pelanggaran Standar Keamanan & Keselamatan Kerja',
            'Ketidaksesuaian Fasilitas dan Konsumsi yang Dijanjikan',
            'Penyalahgunaan Data Pribadi Crew Event',
        ];

        return [
            'ticket_number' => 'RPT-'.date('Ym').'-'.fake()->unique()->numerify('####'),
            'reporter_id' => User::factory()->volunteer(),
            'reported_id' => User::factory()->eo(),
            'reason' => fake()->randomElement($reasons),
            'description' => fake('id_ID')->paragraph(3),
            'evidence_path' => null,
            'status' => ReportStatus::Pending,
            'action_taken' => null,
            'admin_notes' => null,
            'resolved_by' => null,
            'resolved_at' => null,
        ];
    }

    public function inReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::InReview,
            'admin_notes' => 'Laporan telah diterima tim moderasi dan sedang dalam proses klarifikasi ke kedua belah pihak.',
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Resolved,
            'action_taken' => fake()->randomElement([
                'Mediasi Selesai & Kompensasi Diberikan',
                'Pemberian Peringatan Tertulis Pertama',
                'Penyesuaian Jam Kerja & Fasilitas Lapangan',
            ]),
            'admin_notes' => 'Telah dilakukan investigasi dan konfirmasi kepada pihak penyelenggara serta pelapor. Kasus dinyatakan selesai dengan kesepakatan bersama.',
            'resolved_at' => now()->subDays(fake()->numberBetween(1, 14)),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Rejected,
            'action_taken' => 'Laporan Ditolak',
            'admin_notes' => 'Setelah dilakukan penelusuran, bukti yang dilampirkan tidak mencukupi dan kronologi tidak terbukti melanggar ketentuan platform.',
            'resolved_at' => now()->subDays(fake()->numberBetween(1, 7)),
        ]);
    }
}
