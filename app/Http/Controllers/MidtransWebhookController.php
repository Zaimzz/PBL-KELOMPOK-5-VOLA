<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle a Midtrans payment notification (webhook / callback).
     *
     * Midtrans sends HTTP POST with JSON body. This handler:
     *  1. Verifies the signature_key (SHA-512 of orderId + statusCode + grossAmount + serverKey).
     *  2. Finds the matching Payment by order_id.
     *  3. Maps transaction_status → PaymentStatus.
     *  4. Applies the update idempotently (safe to replay).
     *  5. Activates event publication on successful payment.
     *
     * Security:
     *  - Signature verified before any DB changes.
     *  - Route is excluded from CSRF via bootstrap/app.php.
     *  - Server Key is never logged.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Required fields guard
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;

        if (! $orderId || ! $transactionStatus || ! $signatureKey) {
            Log::warning('Midtrans webhook: missing required fields', [
                'has_order_id' => (bool) $orderId,
                'has_status' => (bool) $transactionStatus,
                'has_signature' => (bool) $signatureKey,
            ]);

            return response()->json(['error' => 'Missing required fields'], 400);
        }

        // Signature verification: SHA-512(orderId + statusCode + grossAmount + serverKey)
        $serverKey = config('services.midtrans.server_key', '');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if (! hash_equals($expectedSignature, (string) $signatureKey)) {
            Log::warning('Midtrans webhook: invalid signature', ['order_id' => $orderId]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        /** @var Payment|null $payment */
        $payment = Payment::where('midtrans_order_id', $orderId)->first();

        if (! $payment) {
            Log::warning('Midtrans webhook: order_id not found', ['order_id' => $orderId]);

            // Return 200 so Midtrans stops retrying an order we do not recognise
            return response()->json(['message' => 'Order not found'], 200);
        }

        // Determine the canonical new status
        $newStatus = $this->resolveStatus($transactionStatus, $fraudStatus);

        // Idempotency guard: do not reprocess terminal payments unless upgrading to paid
        $currentStatusValue = $payment->status instanceof PaymentStatus
            ? $payment->status->value
            : (string) $payment->status;

        $terminalStatuses = ['paid', 'failed', 'expired', 'cancelled'];

        if (in_array($currentStatusValue, $terminalStatuses, true) && $newStatus !== PaymentStatus::Paid) {
            // Already in a terminal state; return 200 without touching anything
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Build update payload
        $updateData = [
            'status' => $newStatus,
            'transaction_id' => $transactionId,
            'payment_type' => $paymentType,
            'payload' => $payload,
        ];

        if ($newStatus === PaymentStatus::Paid && ! $payment->paid_at) {
            $updateData['paid_at'] = now();
        }

        $payment->update($updateData);

        // Activate event publication only on successful payment
        if ($newStatus === PaymentStatus::Paid) {
            $this->activatePublication($payment);
        }

        Log::info('Midtrans webhook: processed', [
            'order_id' => $orderId,
            'new_status' => $newStatus->value,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Map Midtrans transaction_status + fraud_status to our PaymentStatus enum.
     */
    private function resolveStatus(string $transactionStatus, ?string $fraudStatus): PaymentStatus
    {
        return match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => PaymentStatus::Paid,
            $transactionStatus === 'settlement' => PaymentStatus::Paid,
            $transactionStatus === 'pending' => PaymentStatus::Pending,
            $transactionStatus === 'deny' => PaymentStatus::Failed,
            $transactionStatus === 'expire' => PaymentStatus::Expired,
            $transactionStatus === 'cancel' => PaymentStatus::Cancelled,
            default => PaymentStatus::Pending,
        };
    }

    /**
     * Activate the event publication for a successfully paid package.
     * Guards against double-activation are handled by checking paid_at above.
     */
    private function activatePublication(Payment $payment): void
    {
        $event = $payment->event;

        if (! $event) {
            return;
        }

        // Only activate if the event is awaiting payment or already active
        // (do not accidentally re-activate a closed/finished/rejected event)
        $activatableStatuses = [
            EventStatus::AwaitingPayment->value,
            EventStatus::Active->value,
        ];

        $currentStatusValue = $event->status instanceof EventStatus
            ? $event->status->value
            : (string) $event->status;

        if (! in_array($currentStatusValue, $activatableStatuses, true)) {
            return;
        }

        $event->update([
            'status' => EventStatus::Active,
            'published_at' => $event->published_at ?? now(),
        ]);

        Log::info('Midtrans webhook: event publication activated', [
            'event_id' => $event->id,
            'package_duration_days' => $payment->package_duration_days,
        ]);
    }
}
