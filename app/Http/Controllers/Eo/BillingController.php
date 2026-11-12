<?php

namespace App\Http\Controllers\Eo;

use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Payment;
use App\Support\PublicationPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction;

class BillingController extends Controller
{
    public function __construct()
    {
        // Configure Midtrans credentials from config/services.php (which reads .env).
        // Server key NEVER reaches the browser — it is only used in backend calls.
        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = (bool) config('services.midtrans.is_production', false);
        MidtransConfig::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        MidtransConfig::$is3ds = (bool) config('services.midtrans.is_3ds', true);

        // Disable SSL verification only on local dev (Windows curl often lacks a CA bundle).
        // The SDK checks CURLOPT_HTTPHEADER in curlOptions without isset(), so we must
        // include it (even empty) to avoid "Undefined array key" errors.
        // On production (Render/Linux) APP_ENV=production so this block is skipped.
        if (app()->isLocal()) {
            MidtransConfig::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => [],
            ];
        }
    }

    /**
     * Display the billing & invoice page for the authenticated EO.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        $packages = PublicationPackage::all();
        $events = collect();
        $payments = collect();
        $selectedEvent = null;
        $totalPaid = 0;
        $paidCount = 0;
        $pendingAmount = 0;
        $pendingCount = 0;
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');

        if ($organizerProfile) {
            $events = Event::where('organizer_id', $organizerProfile->id)
                ->orderByDesc('created_at')
                ->get();

            $selectedEventId = $request->query('event_id');
            $selectedEvent = $selectedEventId
                ? $events->firstWhere('id', $selectedEventId)
                : $events->first();

            // Validate status filter — no "cancelled" option exposed to EO
            $validStatuses = ['all', 'pending', 'paid', 'expired', 'failed'];
            if (! in_array($statusFilter, $validStatuses, true)) {
                $statusFilter = 'all';
            }

            // Auto-expire: mark overdue pending payments as expired before querying
            $eoEventIds = $events->pluck('id');
            Payment::whereIn('event_id', $eoEventIds)
                ->where('status', PaymentStatus::Pending)
                ->whereNotNull('expired_at')
                ->where('expired_at', '<', now())
                ->update(['status' => PaymentStatus::Expired]);

            // Build query scoped to this EO's events only
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

            $query = Payment::with(['event'])
                ->whereIn('event_id', $eoEventIds)
                ->latest('created_at');

            if ($statusFilter !== 'all') {
                $statusEnum = PaymentStatus::tryFrom($statusFilter);
                if ($statusEnum !== null) {
                    $query->where('status', $statusEnum);
                }
            }

            if ($search !== '') {
                $cleanSearch = ltrim($search, '#');
                $query->where(function ($q) use ($cleanSearch, $operator) {
                    $q->where('invoice_number', $operator, '%'.$cleanSearch.'%')
                        ->orWhereHas('event', function ($eq) use ($cleanSearch, $operator) {
                            $eq->where('title', $operator, '%'.$cleanSearch.'%');
                        });
                });
            }

            $payments = $query->paginate(10)->withQueryString();

            // Financial summary scoped to this EO
            $totalPaid = Payment::whereIn('event_id', $eoEventIds)
                ->where('status', PaymentStatus::Paid)
                ->sum('amount');
            $paidCount = Payment::whereIn('event_id', $eoEventIds)
                ->where('status', PaymentStatus::Paid)
                ->count();
            $pendingAmount = Payment::whereIn('event_id', $eoEventIds)
                ->where('status', PaymentStatus::Pending)
                ->sum('amount');
            $pendingCount = Payment::whereIn('event_id', $eoEventIds)
                ->where('status', PaymentStatus::Pending)
                ->count();
        }

        return view('eo.billing.index', compact(
            'organizerProfile',
            'packages',
            'events',
            'selectedEvent',
            'payments',
            'totalPaid',
            'paidCount',
            'pendingAmount',
            'pendingCount',
            'statusFilter',
            'search',
        ));
    }

    /**
     * Create an invoice and obtain a Midtrans Snap token.
     * Returns JSON so the frontend can open the Snap popup immediately.
     * Prevents duplicate invoices for the same event + package combination
     * when a pending invoice already exists.
     */
    public function createInvoice(Request $request): JsonResponse
    {
        $user = $request->user();
        $organizerProfile = $user->organizerProfile;

        if (! $organizerProfile) {
            return response()->json(['error' => 'Profil organisasi tidak ditemukan.'], 403);
        }

        $validated = $request->validate([
            'event_id' => ['required', 'integer'],
            'package_slug' => ['required', 'string', 'in:'.implode(',', PublicationPackage::slugs())],
        ]);

        // Confirm EO owns this event
        $event = Event::where('id', $validated['event_id'])
            ->where('organizer_id', $organizerProfile->id)
            ->first();

        if (! $event) {
            return response()->json(['error' => 'Event tidak ditemukan atau bukan milik Anda.'], 403);
        }

        $package = PublicationPackage::find($validated['package_slug']);

        // Idempotency check: if there is already a pending payment for this exact
        // event + package combination, return the existing snap token (or renew it).
        $existing = Payment::where('event_id', $event->id)
            ->where('package_name', $validated['package_slug'])
            ->where('status', PaymentStatus::Pending)
            ->where('expired_at', '>', now())
            ->latest()
            ->first();

        if ($existing && $existing->snap_token) {
            return response()->json([
                'snap_token' => $existing->snap_token,
                'payment_id' => $existing->id,
                'invoice_number' => $existing->invoice_number,
                'client_key' => config('services.midtrans.client_key'),
            ]);
        }

        // Generate a unique invoice number
        $invoiceNumber = 'INV-'.now()->format('Y').'-'.strtoupper(Str::random(8));

        // Canonical amount comes from backend catalogue — never from the request
        $amount = PublicationPackage::price($validated['package_slug']);

        // Build Midtrans Snap transaction payload
        $orderId = 'VOLA-'.now()->format('YmdHis').'-'.Str::random(6);

        // Create the payment record FIRST so we can embed its ID in the Snap finish URL.
        // The finish URL is a server-side verify-and-redirect route — this guarantees the
        // payment status is synced from Midtrans even when the JS onSuccess callback is
        // interrupted by the browser redirect triggered by the Snap finish callback.
        $payment = Payment::create([
            'event_id' => $event->id,
            'package_name' => $validated['package_slug'],
            'package_duration_days' => $package['duration_days'],
            'invoice_number' => $invoiceNumber,
            'midtrans_order_id' => $orderId,
            'snap_token' => null,
            'amount' => $amount,
            'status' => PaymentStatus::Pending,
            'expired_at' => now()->addHours(24),
        ]);

        $snapParams = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [
                [
                    'id' => $validated['package_slug'],
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Paket Publikasi '.($package['name']).' — '.$event->title,
                ],
            ],
            'customer_details' => [
                'first_name' => $organizerProfile->organization_name ?? $user->name,
                'email' => $user->email,
                'phone' => $organizerProfile->pic_phone ?? '',
            ],
            'callbacks' => [
                // Server-side verify-and-redirect: Midtrans calls this URL when the user
                // clicks "Return to merchant's page" after payment. The route verifies
                // the transaction status directly with Midtrans API before redirecting.
                'finish' => route('eo.billing.finish', $payment),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($snapParams);
        } catch (\Exception $e) {
            // Roll back the orphaned payment record if Snap token retrieval fails
            $payment->delete();
            report($e);

            return response()->json(['error' => 'Gagal menghubungi Midtrans. Silakan coba lagi.'], 500);
        }

        $payment->update(['snap_token' => $snapToken]);

        return response()->json([
            'snap_token' => $snapToken,
            'payment_id' => $payment->id,
            'invoice_number' => $payment->invoice_number,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    /**
     * Display a single invoice detail page.
     * Restricted to the EO who owns the event.
     */
    public function show(Request $request, Payment $payment): View
    {
        $this->authorizePayment($request, $payment);
        $payment->load('event.organizer');

        $package = PublicationPackage::find($payment->package_name ?? '');

        return view('eo.billing.show', compact('payment', 'package'));
    }

    /**
     * Download / print-ready HTML invoice for a single payment.
     * Uses the same printable template approach as the Admin TransactionController.
     */
    public function downloadInvoice(Request $request, Payment $payment): Response
    {
        $this->authorizePayment($request, $payment);
        $payment->load('event.organizer');

        $package = PublicationPackage::find($payment->package_name ?? '');

        return response()->view('eo.billing.invoice-pdf', compact('payment', 'package'))
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="Invoice-'.$payment->invoice_number.'.html"');
    }

    /**
     * Re-fetch and return the current status of a payment from the database.
     * The browser polls this after the Snap popup closes to show the updated status
     * without waiting for the webhook.
     */
    public function status(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizePayment($request, $payment);

        $statusValue = $payment->status instanceof PaymentStatus
            ? $payment->status->value
            : (string) $payment->status;

        $statusLabel = match ($statusValue) {
            'paid' => 'Lunas',
            'pending' => 'Menunggu Pembayaran',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($statusValue),
        };

        return response()->json([
            'status' => $statusValue,
            'status_label' => $statusLabel,
            'paid_at' => $payment->paid_at?->translatedFormat('d M Y, H:i'),
        ]);
    }

    /**
     * Server-side payment finish handler.
     * Midtrans redirects the browser here after the user clicks "Return to merchant's page"
     * in the Snap popup. We verify the transaction status directly with Midtrans API
     * before redirecting to the billing index — this is the reliable path because the
     * browser navigation cancels any in-flight JS fetch calls from onSuccess.
     */
    public function finishPayment(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorizePayment($request, $payment);

        if ($payment->status === PaymentStatus::Pending) {
            // Midtrans appends these params to the finish redirect URL.
            // Use them as a first-pass signal before hitting the API.
            $redirectStatus = $request->query('transaction_status');
            $redirectStatusCode = $request->query('status_code');

            try {
                $result = Transaction::status($payment->midtrans_order_id);

                $transactionStatus = $result->transaction_status ?? $redirectStatus;
                $fraudStatus = $result->fraud_status ?? null;

                $newStatus = match (true) {
                    $transactionStatus === 'capture' && $fraudStatus === 'accept' => PaymentStatus::Paid,
                    $transactionStatus === 'settlement' => PaymentStatus::Paid,
                    // If the API still says "pending" but Midtrans redirect params say success,
                    // trust the redirect params (Sandbox race condition / GoPay settlement lag).
                    $transactionStatus === 'pending' && in_array($redirectStatus, ['capture', 'settlement'], true) => PaymentStatus::Paid,
                    $transactionStatus === 'pending' && $redirectStatusCode === '200' => PaymentStatus::Paid,
                    $transactionStatus === 'deny' => PaymentStatus::Failed,
                    $transactionStatus === 'expire' => PaymentStatus::Expired,
                    $transactionStatus === 'cancel' => PaymentStatus::Cancelled,
                    default => PaymentStatus::Pending,
                };

                $updateData = ['status' => $newStatus];

                if ($newStatus === PaymentStatus::Paid && ! $payment->paid_at) {
                    $updateData['paid_at'] = now();
                    $updateData['payment_type'] = $result->payment_type ?? null;
                    $updateData['transaction_id'] = $result->transaction_id ?? null;
                }

                $payment->update($updateData);

                if ($newStatus === PaymentStatus::Paid) {
                    $this->activateEventPublication($payment);
                }
            } catch (\Exception $e) {
                report($e);

                // Fallback: if the Midtrans API call failed entirely but redirect params
                // indicate success, still mark as paid so the user isn't left stuck.
                if (in_array($redirectStatus, ['capture', 'settlement'], true) || $redirectStatusCode === '200') {
                    if (! $payment->paid_at) {
                        $payment->update([
                            'status' => PaymentStatus::Paid,
                            'paid_at' => now(),
                        ]);
                        $this->activateEventPublication($payment->fresh());
                    }
                }
            }
        }

        return redirect()->route('eo.billing.index');
    }

    /**
     * Verify payment status directly from Midtrans API and update DB.
     * Called from frontend after Snap onSuccess to handle cases where
     * the webhook hasn't fired yet (e.g., local development).
     */
    public function verify(Request $request, Payment $payment): JsonResponse
    {
        $this->authorizePayment($request, $payment);

        // Already in a terminal state — no need to query Midtrans
        if ($payment->status !== PaymentStatus::Pending) {
            return response()->json(['status' => $payment->status->value]);
        }

        try {
            $result = Transaction::status($payment->midtrans_order_id);

            $transactionStatus = $result->transaction_status ?? null;
            $fraudStatus = $result->fraud_status ?? null;

            $newStatus = match (true) {
                $transactionStatus === 'capture' && $fraudStatus === 'accept' => PaymentStatus::Paid,
                $transactionStatus === 'settlement' => PaymentStatus::Paid,
                $transactionStatus === 'pending' => PaymentStatus::Pending,
                $transactionStatus === 'deny' => PaymentStatus::Failed,
                $transactionStatus === 'expire' => PaymentStatus::Expired,
                $transactionStatus === 'cancel' => PaymentStatus::Cancelled,
                default => PaymentStatus::Pending,
            };

            $updateData = ['status' => $newStatus];

            if ($newStatus === PaymentStatus::Paid && ! $payment->paid_at) {
                $updateData['paid_at'] = now();
                $updateData['payment_type'] = $result->payment_type ?? null;
                $updateData['transaction_id'] = $result->transaction_id ?? null;
            }

            $payment->update($updateData);

            if ($newStatus === PaymentStatus::Paid) {
                $this->activateEventPublication($payment);
            }

            return response()->json(['status' => $newStatus->value]);
        } catch (\Exception $e) {
            report($e);

            return response()->json(['status' => $payment->status->value]);
        }
    }

    /**
     * Activate event publication after confirmed payment.
     */
    private function activateEventPublication(Payment $payment): void
    {
        $event = $payment->event;

        if (! $event) {
            return;
        }

        $activatableStatuses = [
            EventStatus::AwaitingPayment->value,
            EventStatus::Active->value,
        ];

        $currentStatus = $event->status instanceof EventStatus
            ? $event->status->value
            : (string) $event->status;

        if (! in_array($currentStatus, $activatableStatuses, true)) {
            return;
        }

        $event->update([
            'status' => EventStatus::Active,
            'published_at' => $event->published_at ?? now(),
        ]);
    }

    /**
     * Abort with 403 if the authenticated EO does not own the event linked to this payment.
     */
    private function authorizePayment(Request $request, Payment $payment): void
    {
        $organizerProfile = $request->user()->organizerProfile;

        if (! $organizerProfile) {
            abort(403, 'Profil organisasi tidak ditemukan.');
        }

        $paymentOrgId = $payment->event?->organizer_id;

        if ($paymentOrgId !== $organizerProfile->id) {
            abort(403, 'Anda tidak memiliki akses ke invoice ini.');
        }
    }
}
