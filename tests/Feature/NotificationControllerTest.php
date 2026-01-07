<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function mockSignatureValid(): void
    {
        $fake = new class extends MidtransService {
            public function __construct() {}
            public function verifyNotificationSignature(string $a, string $b, string $c, string $d): bool
            {
                return true;
            }
        };
        $this->app->instance(MidtransService::class, $fake);
    }

    public function test_webhook_updates_payment_and_campaign_on_settlement(): void
    {
        $this->mockSignatureValid();

        $campaign = Campaign::factory()->create(['raised_amount' => 0]);
        $payment = Payment::factory()->create([
            'campaign_id' => $campaign->id,
            'status' => 'pending',
            'transaction_status' => 'pending',
            'amount' => 50000,
            'midtrans_order_id' => 'ORDER-SETTLE-1',
        ]);

        $payload = [
            'order_id' => 'ORDER-SETTLE-1',
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => (string) $payment->amount,
            'signature_key' => 'dummy',
            'transaction_id' => 'txn_123',
        ];

        $res = $this->postJson(route('payment.notification'), $payload);
        $res->assertOk();

        $payment->refresh();
        $campaign->refresh();
        $this->assertEquals('paid', $payment->status);
        $this->assertEquals('settlement', $payment->transaction_status);
        $this->assertNotNull($payment->paid_at);
        $this->assertEquals(50000, $campaign->raised_amount);
    }

    public function test_webhook_returns_ok_even_when_payment_not_found(): void
    {
        $this->mockSignatureValid();

        $payload = [
            'order_id' => 'NOT-EXISTS',
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '10000',
            'signature_key' => 'dummy',
        ];

        $res = $this->postJson(route('payment.notification'), $payload);
        $res->assertOk();
    }
}


