<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'invoice_number' => 'INV-'.now()->year.'-'.fake()->unique()->numerify('###'),
            'midtrans_order_id' => 'ORDER-'.Str::random(12),
            'snap_token' => null,
            'transaction_id' => null,
            'payment_type' => null,
            'amount' => (int) config('vola.listing_fee', 450000),
            'status' => PaymentStatus::Pending,
            'paid_at' => null,
            'expired_at' => now()->addHours(24),
            'payload' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
            'payment_type' => 'qris',
            'transaction_id' => fake()->uuid(),
        ]);
    }
}
