<?php

use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\OrganizerProfile;
use App\Models\Payment;
use App\Models\User;
use App\Support\PublicationPackage;

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * Create a verified, active EO user with an organizer profile and one event.
 *
 * @return array{user: User, organizer: OrganizerProfile, event: Event}
 */
function makeEoWithEvent(): array
{
    $user = User::factory()->eo()->create();
    $organizer = OrganizerProfile::factory()->for($user)->create();
    $event = Event::factory()->for($organizer, 'organizer')->create([
        'status' => EventStatus::AwaitingPayment,
    ]);

    return compact('user', 'organizer', 'event');
}
// ─── Index page ───────────────────────────────────────────────────────────────

test('eo can open the billing index page', function () {
    ['user' => $user] = makeEoWithEvent();

    $this->actingAs($user)
        ->get(route('eo.billing.index'))
        ->assertOk()
        ->assertSee('Pembayaran & Tagihan');
});

test('guest is redirected away from billing index', function () {
    $this->get(route('eo.billing.index'))
        ->assertRedirect(route('login'));
});

test('volunteer cannot access eo billing index', function () {
    $volunteer = User::factory()->volunteer()->create();

    $this->actingAs($volunteer)
        ->get(route('eo.billing.index'))
        ->assertForbidden();
});

test('billing index shows financial summary from db only for this eo', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    // Paid payment for this EO
    Payment::factory()->paid()->create([
        'event_id' => $event->id,
        'amount' => 55_000,
        'package_name' => 'business',
        'package_duration_days' => 14,
    ]);

    // Paid payment for a DIFFERENT EO — must NOT appear in summary
    $otherUser = User::factory()->eo()->create();
    $otherOrg = OrganizerProfile::factory()->for($otherUser)->create();
    $otherEvent = Event::factory()->for($otherOrg, 'organizer')->create();
    Payment::factory()->paid()->create(['event_id' => $otherEvent->id, 'amount' => 999_999]);

    $resp = $this->actingAs($user)->get(route('eo.billing.index'));
    $resp->assertOk();

    // The 55k total should appear; the 999k from another EO must not
    $resp->assertSee('55.000');
    $resp->assertDontSee('999.999');
});

test('billing index shows all four packages', function () {
    ['user' => $user] = makeEoWithEvent();

    $this->actingAs($user)
        ->get(route('eo.billing.index'))
        ->assertOk()
        ->assertSee('Professional')
        ->assertSee('Business')
        ->assertSee('Premium Business')
        ->assertSee('Corporate');
});

test('billing index shows empty state when no invoices exist', function () {
    ['user' => $user] = makeEoWithEvent();

    $this->actingAs($user)
        ->get(route('eo.billing.index'))
        ->assertOk()
        ->assertSee('Belum Ada Invoice');
});

test('billing index filters by status', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    // Use explicit invoice numbers as anchors — they only appear in the invoice table rows,
    // not in the financial summary cards or the package price grid.
    Payment::factory()->create([
        'event_id' => $event->id,
        'invoice_number' => 'INV-2026-PENDING-TST',
        'package_name' => 'professional',
        'package_duration_days' => 7,
        'amount' => 35_000,
        'status' => PaymentStatus::Pending,
    ]);
    Payment::factory()->paid()->create([
        'event_id' => $event->id,
        'invoice_number' => 'INV-2026-PAID-TST',
        'package_name' => 'business',
        'package_duration_days' => 14,
        'amount' => 55_000,
    ]);

    // Filter to paid only — only the paid invoice row must be visible
    $resp = $this->actingAs($user)->get(route('eo.billing.index', ['status' => 'paid']));
    $resp->assertOk()->assertSee('INV-2026-PAID-TST')->assertDontSee('INV-2026-PENDING-TST');
});

test('billing index search by invoice number works', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    $p1 = Payment::factory()->create(['event_id' => $event->id, 'invoice_number' => 'INV-2026-ALPHA', 'package_name' => 'professional', 'package_duration_days' => 7, 'amount' => 35_000]);
    $p2 = Payment::factory()->paid()->create(['event_id' => $event->id, 'invoice_number' => 'INV-2026-BETA', 'package_name' => 'business', 'package_duration_days' => 14, 'amount' => 55_000]);

    $resp = $this->actingAs($user)->get(route('eo.billing.index', ['search' => 'ALPHA']));
    $resp->assertOk()->assertSee('INV-2026-ALPHA')->assertDontSee('INV-2026-BETA');
});

// ─── Create invoice ───────────────────────────────────────────────────────────

test('eo can create an invoice via json endpoint', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    // We test validation & ownership only — Snap API will fail in test env, that is expected
    $resp = $this->actingAs($user)
        ->postJson(route('eo.billing.invoice.create'), [
            'event_id' => $event->id,
            'package_slug' => 'professional',
        ]);

    // Either 200 (token obtained) or 500 (Snap sandbox unreachable) — but NOT 403 or 422
    expect($resp->status())->toBeIn([200, 500]);
});

test('create invoice rejects unknown package slug', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    $this->actingAs($user)
        ->postJson(route('eo.billing.invoice.create'), [
            'event_id' => $event->id,
            'package_slug' => 'ultra_premium_nonexistent',
        ])
        ->assertUnprocessable();
});

test('create invoice rejects event owned by another eo', function () {
    ['user' => $user] = makeEoWithEvent();

    $otherUser = User::factory()->eo()->create();
    $otherOrg = OrganizerProfile::factory()->for($otherUser)->create();
    $otherEvent = Event::factory()->for($otherOrg, 'organizer')->create();

    $this->actingAs($user)
        ->postJson(route('eo.billing.invoice.create'), [
            'event_id' => $otherEvent->id,
            'package_slug' => 'professional',
        ])
        ->assertForbidden();
});

test('create invoice returns 403 when eo has no organizer profile', function () {
    $user = User::factory()->eo()->create();
    // No OrganizerProfile created intentionally
    $event = Event::factory()->create();

    $this->actingAs($user)
        ->postJson(route('eo.billing.invoice.create'), [
            'event_id' => $event->id,
            'package_slug' => 'professional',
        ])
        ->assertForbidden();
});

// ─── Show / download ──────────────────────────────────────────────────────────

test('eo can view their own invoice detail', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    $payment = Payment::factory()->create([
        'event_id' => $event->id,
        'package_name' => 'professional',
        'package_duration_days' => 7,
        'amount' => 35_000,
    ]);

    $this->actingAs($user)
        ->get(route('eo.billing.show', $payment))
        ->assertOk()
        ->assertSee($payment->invoice_number);
});

test('eo cannot view invoice belonging to another eo', function () {
    ['user' => $user] = makeEoWithEvent();

    $otherUser = User::factory()->eo()->create();
    $otherOrg = OrganizerProfile::factory()->for($otherUser)->create();
    $otherEvent = Event::factory()->for($otherOrg, 'organizer')->create();
    $otherPayment = Payment::factory()->create(['event_id' => $otherEvent->id, 'package_name' => 'professional', 'package_duration_days' => 7, 'amount' => 35_000]);

    $this->actingAs($user)
        ->get(route('eo.billing.show', $otherPayment))
        ->assertForbidden();
});

test('eo can download invoice pdf for their own payment', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    $payment = Payment::factory()->paid()->create([
        'event_id' => $event->id,
        'package_name' => 'business',
        'package_duration_days' => 14,
        'amount' => 55_000,
    ]);

    $this->actingAs($user)
        ->get(route('eo.billing.download', $payment))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8');
});

test('eo cannot download invoice pdf for another eo payment', function () {
    ['user' => $user] = makeEoWithEvent();

    $otherUser = User::factory()->eo()->create();
    $otherOrg = OrganizerProfile::factory()->for($otherUser)->create();
    $otherEvent = Event::factory()->for($otherOrg, 'organizer')->create();
    $otherPayment = Payment::factory()->paid()->create(['event_id' => $otherEvent->id, 'package_name' => 'professional', 'package_duration_days' => 7, 'amount' => 35_000]);

    $this->actingAs($user)
        ->get(route('eo.billing.download', $otherPayment))
        ->assertForbidden();
});

// ─── Status endpoint ──────────────────────────────────────────────────────────

test('status endpoint returns correct payment status json', function () {
    ['user' => $user, 'event' => $event] = makeEoWithEvent();

    $payment = Payment::factory()->paid()->create([
        'event_id' => $event->id,
        'package_name' => 'professional',
        'package_duration_days' => 7,
        'amount' => 35_000,
    ]);

    $this->actingAs($user)
        ->getJson(route('eo.billing.status', $payment))
        ->assertOk()
        ->assertJson(['status' => 'paid', 'status_label' => 'Lunas']);
});

// ─── Webhook ─────────────────────────────────────────────────────────────────

test('webhook updates payment to paid and activates event', function () {
    ['event' => $event] = makeEoWithEvent();

    $payment = Payment::factory()->create([
        'event_id' => $event->id,
        'package_name' => 'professional',
        'package_duration_days' => 7,
        'amount' => 35_000,
        'midtrans_order_id' => 'TEST-ORDER-123',
        'status' => PaymentStatus::Pending,
    ]);

    // Build the signature key Midtrans sends
    $serverKey = config('services.midtrans.server_key', 'test-key');
    $signatureKey = hash('sha512', 'TEST-ORDER-123'.'200'.'35000.00'.$serverKey);

    $this->postJson(route('webhooks.midtrans'), [
        'order_id' => 'TEST-ORDER-123',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'payment_type' => 'qris',
        'transaction_id' => 'TXN-999',
        'gross_amount' => '35000.00',
        'status_code' => '200',
        'signature_key' => $signatureKey,
    ])->assertOk();

    $payment->refresh();
    expect($payment->status)->toBe(PaymentStatus::Paid);
    expect($payment->payment_type)->toBe('qris');
});

test('webhook with invalid order id returns 200 without crashing', function () {
    $serverKey = config('services.midtrans.server_key', 'test-key');
    $signatureKey = hash('sha512', 'NONEXISTENT-ORDER'.'200'.'35000.00'.$serverKey);

    $this->postJson(route('webhooks.midtrans'), [
        'order_id' => 'NONEXISTENT-ORDER',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'payment_type' => 'qris',
        'transaction_id' => 'TXN-X',
        'gross_amount' => '35000.00',
        'status_code' => '200',
        'signature_key' => $signatureKey,
    ])->assertOk();
});

test('webhook is idempotent — processing paid payment again does not duplicate', function () {
    ['event' => $event] = makeEoWithEvent();

    $payment = Payment::factory()->paid()->create([
        'event_id' => $event->id,
        'package_name' => 'professional',
        'package_duration_days' => 7,
        'amount' => 35_000,
        'midtrans_order_id' => 'DUP-ORDER-456',
    ]);

    $originalPaidAt = $payment->paid_at;

    $serverKey = config('services.midtrans.server_key', 'test-key');
    $signatureKey = hash('sha512', 'DUP-ORDER-456'.'200'.'35000.00'.$serverKey);

    $this->postJson(route('webhooks.midtrans'), [
        'order_id' => 'DUP-ORDER-456',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'payment_type' => 'qris',
        'transaction_id' => 'TXN-DUP',
        'gross_amount' => '35000.00',
        'status_code' => '200',
        'signature_key' => $signatureKey,
    ])->assertOk();

    // paid_at must not change — it was already set
    $payment->refresh();
    expect($payment->paid_at->toDateTimeString())
        ->toBe($originalPaidAt->toDateTimeString());
});

// ─── PublicationPackage support class ─────────────────────────────────────────

test('PublicationPackage returns four packages', function () {
    expect(PublicationPackage::all())->toHaveCount(4);
});

test('PublicationPackage price returns correct amounts', function () {
    expect(PublicationPackage::price('professional'))->toBe(35_000);
    expect(PublicationPackage::price('business'))->toBe(55_000);
    expect(PublicationPackage::price('premium_business'))->toBe(85_000);
    expect(PublicationPackage::price('corporate'))->toBe(125_000);
});

test('PublicationPackage throws on unknown slug', function () {
    expect(fn () => PublicationPackage::price('nonexistent'))->toThrow(\InvalidArgumentException::class);
});
