<?php

namespace Tests\Feature\Admin;

use App\Enums\ReportStatus;
use App\Enums\UserStatus;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $reporter;

    private User $reported;

    private Report $report;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->reporter = User::factory()->create(['role' => 'volunteer', 'name' => 'Pelapor Andi']);
        $this->reported = User::factory()->create(['role' => 'eo', 'name' => 'Terlapor Bima', 'status' => 'active']);

        $this->report = Report::create([
            'ticket_number' => 'RPT-2025-001',
            'reporter_id' => $this->reporter->id,
            'reported_id' => $this->reported->id,
            'reason' => 'Informasi Palsu',
            'description' => 'Event yang dipublikasikan tidak sesuai dengan kriteria yang dijanjikan.',
            'status' => ReportStatus::Pending,
        ]);
    }

    public function test_admin_can_access_reports_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.index');
        $response->assertSee('Laporan Pengguna');
        $response->assertSee('RPT-2025-001');
        $response->assertSee('Pelapor Andi');
        $response->assertSee('Terlapor Bima');
        $response->assertSee('Menunggu Tindakan');
        $response->assertSee('Sedang Ditinjau');
        $response->assertSee('Laporan Selesai');
    }

    public function test_non_admin_cannot_access_reports_index(): void
    {
        $response = $this->actingAs($this->reporter)->get(route('admin.reports.index'));
        $response->assertStatus(403);

        $responseEo = $this->actingAs($this->reported)->get(route('admin.reports.index'));
        $responseEo->assertStatus(403);
    }

    public function test_filter_and_search_reports(): void
    {
        $resolvedReport = Report::create([
            'ticket_number' => 'RPT-2025-002',
            'reporter_id' => $this->reporter->id,
            'reported_id' => $this->reported->id,
            'reason' => 'Perilaku Tidak Pantas',
            'description' => 'Ada kata-kata kasar dalam koordinasi chat.',
            'status' => ReportStatus::Resolved,
        ]);

        // 1. Search by ticket number
        $resSearch = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'search' => 'RPT-2025-001',
        ]));
        $resSearch->assertStatus(200);
        $resSearch->assertSee('RPT-2025-001');
        $resSearch->assertDontSee('RPT-2025-002');

        // 2. Filter by status: pending
        $resPending = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'status' => 'pending',
        ]));
        $resPending->assertStatus(200);
        $resPending->assertSee('RPT-2025-001');
        $resPending->assertDontSee('RPT-2025-002');

        // 3. Filter by status: resolved
        $resResolved = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'status' => 'resolved',
        ]));
        $resResolved->assertStatus(200);
        $resResolved->assertSee('RPT-2025-002');
        $resResolved->assertDontSee('RPT-2025-001');
    }

    public function test_admin_can_view_report_detail_json(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.reports.show', $this->report));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $this->report->id,
                'ticket_number' => 'RPT-2025-001',
                'reason' => 'Informasi Palsu',
                'status' => 'pending',
                'reporter' => [
                    'id' => $this->reporter->id,
                    'name' => 'Pelapor Andi',
                ],
                'reported' => [
                    'id' => $this->reported->id,
                    'name' => 'Terlapor Bima',
                ],
            ],
        ]);
    }

    public function test_admin_can_take_action_warning(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.reports.action', $this->report), [
            'action' => 'warning',
            'admin_notes' => 'Harap patuhi ketentuan layanan.',
        ]);

        $this->report->refresh();
        $this->assertEquals(ReportStatus::Resolved, $this->report->status);
        $this->assertEquals('Diberikan Peringatan Resmi', $this->report->action_taken);
        $this->assertEquals('Harap patuhi ketentuan layanan.', $this->report->admin_notes);
        $this->assertEquals($this->admin->id, $this->report->resolved_by);
    }

    public function test_admin_can_take_action_suspend(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.reports.action', $this->report), [
            'action' => 'suspend',
            'admin_notes' => 'Pelanggaran berat, akun ditangguhkan.',
        ]);

        $this->report->refresh();
        $this->reported->refresh();

        $this->assertEquals(ReportStatus::Resolved, $this->report->status);
        $this->assertEquals('Akun Ditangguhkan', $this->report->action_taken);
        $this->assertEquals(UserStatus::Suspended, $this->reported->status);
    }

    public function test_admin_can_take_action_reject(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.reports.action', $this->report), [
            'action' => 'reject',
            'admin_notes' => 'Bukti tidak mencukupi, aduan digugurkan.',
        ]);

        $this->report->refresh();
        $this->assertEquals(ReportStatus::Rejected, $this->report->status);
        $this->assertEquals('Laporan Ditolak (Tidak Terbukti)', $this->report->action_taken);
    }
}
