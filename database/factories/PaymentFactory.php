<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $method = $this->faker->randomElement(['qris', 'ewallet', 'virtual_account']);
        $orderId = strtoupper($method) . '-' . time() . '-' . Str::random(6);

        return [
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
            'donor_name' => null,
            'donor_phone' => null,
            'donor_email' => null,
            'donor_message' => null,
            'is_anonymous' => false,
            'midtrans_order_id' => $orderId,
            'midtrans_transaction_id' => null,
            'payment_method' => $method,
            'payment_channel' => null,
            'status' => 'pending',
            'transaction_status' => 'pending',
            'amount' => $this->faker->numberBetween(10000, 500000),
            'currency' => 'IDR',
            'reference_id' => $orderId,
            'qr_string' => null,
            'qr_url' => null,
            'virtual_account_number' => null,
            'deeplink_url' => null,
            'notification_data' => null,
            'expires_at' => now()->addDay(),
            'paid_at' => null,
        ];
    }

    public function paid(): self
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'transaction_status' => 'settlement',
            'paid_at' => now(),
        ]);
    }
}


