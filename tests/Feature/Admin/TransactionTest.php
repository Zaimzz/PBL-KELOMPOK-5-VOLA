<?php

namespace Tests\Feature\Admin;

use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\VerificationStatus;
use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $eoUser;

    private OrganizerProfile $organizer;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->eoUser = User::factory()->create(['role' => 'eo']);

        $this->organizer = OrganizerProfile::factory()->create([
            'user_id' => $this->eoUser->id,
            'organization_name' => 'PT Pop Media Nusantara',
            'verification_status' => VerificationStatus::Verified,
        ]);

        $this->event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Indonesia Comic Con 2025',
            'status' => EventStatus::Active,
        ]);
    }

    public function test_admin_can_access_transactions_index(): void
    {
        Payment::factory()->create([
            'event_id' => $this->event->id,
            'invoice_number' => 'INV-2025-084',
            'amount' => 450000,
            'status' => PaymentStatus::Paid,
            'payment_type' => 'qris',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.transactions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.transactions.index');
        $response->assertSee('Data Transaksi');
        $response->assertSee('INV-2025-084');
        $response->assertSee('Total Volume Pembayaran');
        $response->assertSee('Transaksi Berhasil');
        $response->assertSee('Menunggu Pembayaran');
    }

    public function test_non_admin_cannot_access_transactions(): void
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);

        $response = $this->actingAs($volunteer)->get(route('admin.transactions.index'));
        $response->assertStatus(403);

        $responseEo = $this->actingAs($this->eoUser)->get(route('admin.transactions.index'));
        $responseEo->assertStatus(403);
    }

    public function test_search_and_pill_filter_transactions(): void
    {
        $paidTx = Payment::factory()->create([
            'event_id' => $this->event->id,
            'invoice_number' => 'INV-2025-084',
            'amount' => 450000,
            'status' => PaymentStatus::Paid,
        ]);

        $otherEvent = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Bandung Creative Summit',
            'status' => EventStatus::Active,
        ]);

        $pendingTx = Payment::factory()->create([
            'event_id' => $otherEvent->id,
            'invoice_number' => 'INV-2025-082',
            'amount' => 400000,
            'status' => PaymentStatus::Pending,
        ]);

        // Search by invoice number
        $responseSearch = $this->actingAs($this->admin)->get(route('admin.transactions.index', [
            'search' => 'INV-2025-084',
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('INV-2025-084');
        $responseSearch->assertDontSee('INV-2025-082');

        // Filter by pill status 'lunas' / 'paid'
        $responsePaid = $this->actingAs($this->admin)->get(route('admin.transactions.index', [
            'status' => 'lunas',
        ]));
        $responsePaid->assertStatus(200);
        $responsePaid->assertSee('INV-2025-084');
        $responsePaid->assertDontSee('INV-2025-082');

        // Filter by pill status 'menunggu' / 'pending'
        $responsePending = $this->actingAs($this->admin)->get(route('admin.transactions.index', [
            'status' => 'menunggu',
        ]));
        $responsePending->assertStatus(200);
        $responsePending->assertSee('INV-2025-082');
        $responsePending->assertDontSee('INV-2025-084');
    }

    public function test_metrics_header_calculated_accurately(): void
    {
        Payment::factory()->create([
            'event_id' => $this->event->id,
            'amount' => 450000,
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        Payment::factory()->create([
            'event_id' => $this->event->id,
            'amount' => 400000,
            'status' => PaymentStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.transactions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_volume'] === 450000
                && $stats['successful_count'] === 1
                && $stats['pending_count'] === 1;
        });
    }

    public function test_show_endpoint_returns_json(): void
    {
        $tx = Payment::factory()->create([
            'event_id' => $this->event->id,
            'invoice_number' => 'INV-2025-099',
            'amount' => 450000,
            'status' => PaymentStatus::Paid,
            'payment_type' => 'qris',
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.transactions.show', $tx));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $tx->id,
            'invoice_number' => 'INV-2025-099',
            'amount' => 450000,
            'status' => 'paid',
            'payment_type' => 'QRIS',
        ]);
    }

    public function test_download_invoice_returns_successful_response(): void
    {
        $tx = Payment::factory()->create([
            'event_id' => $this->event->id,
            'invoice_number' => 'INV-2025-084',
            'status' => PaymentStatus::Paid,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.transactions.download-invoice', $tx));

        $response->assertStatus(200);
        $response->assertSee('INVOICE RESMI');
        $response->assertSee('INV-2025-084');
    }

    public function test_download_monthly_report_returns_successful_response(): void
    {
        Payment::factory()->create([
            'event_id' => $this->event->id,
            'status' => PaymentStatus::Paid,
            'amount' => 450000,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.transactions.download-report', [
            'month' => now()->format('Y-m'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('REKAPITULASI TRANSAKSI');
    }

    public function test_download_csv_returns_streamed_response(): void
    {
        Payment::factory()->create([
            'event_id' => $this->event->id,
            'invoice_number' => 'INV-2025-084',
            'amount' => 450000,
            'status' => PaymentStatus::Paid,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.transactions.download-csv', [
            'month' => now()->format('Y-m'),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_download_excel_returns_valid_response(): void
    {
        Payment::factory()->create([
            'event_id' => $this->event->id,
            'invoice_number' => 'INV-2025-084',
            'amount' => 450000,
            'status' => PaymentStatus::Paid,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.transactions.download-excel', [
            'month' => now()->format('Y-m'),
        ]));

        $response->assertStatus(200);
    }
}
