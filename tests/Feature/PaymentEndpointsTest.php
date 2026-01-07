<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Payment;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function fakeMidtrans(): void
    {
        $fake = new class extends MidtransService {
            public function __construct() {}
            public function createQRIS(array $data): array
            {
                return [
                    'success' => true,
                    'data' => [
                        'transaction_id' => 'txn_qris_123',
                        'transaction_status' => 'pending',
                        'actions' => [
                            ['url' => 'https://qris.example/abc'],
                        ],
                        'expiry_time' => now()->addDay()->toIso8601String(),
                    ],
                ];
            }
            public function createEWalletCharge(array $data): array
            {
                return [
                    'success' => true,
                    'data' => [
                        'transaction_id' => 'txn_ewallet_123',
                        'transaction_status' => 'pending',
                        'actions' => [
                            ['url' => 'app://deeplink/ovo/xyz'],
                        ],
                    ],
                ];
            }
            public function createVirtualAccount(array $data): array
            {
                return [
                    'success' => true,
                    'data' => [
                        'transaction_id' => 'txn_va_123',
                        'transaction_status' => 'pending',
                        'va_numbers' => [
                            ['va_number' => '1234567890'],
                        ],
                        'expiry_time' => now()->addDay()->toIso8601String(),
                    ],
                ];
            }
            public function getTransactionStatus(string $orderId): array
            {
                return [
                    'success' => true,
                    'data' => [
                        'transaction_id' => 'txn_status_123',
                        'transaction_status' => 'settlement',
                    ],
                ];
            }
            public function verifyNotificationSignature(string $a, string $b, string $c, string $d): bool
            {
                return true;
            }
        };

        $this->app->instance(MidtransService::class, $fake);
    }

    public function test_guest_can_create_qris_payment(): void
    {
        $this->fakeMidtrans();
        $campaign = Campaign::factory()->create();

        $payload = [
            'amount' => 15000,
            'donor_name' => 'Guest Donor',
            'donor_phone' => '08123456789',
            'donor_email' => 'guest@example.com',
            'donor_message' => 'Semoga bermanfaat',
            'is_guest' => true,
            'is_anonymous' => false,
        ];

        $res = $this->postJson(route('payment.qris.create', $campaign), $payload);
        $res->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('payment.payment_method', 'qris');

        $this->assertDatabaseHas('payments', [
            'campaign_id' => $campaign->id,
            'amount' => 15000,
            'payment_method' => 'qris',
            'status' => 'pending',
        ]);
    }

    public function test_user_can_create_virtual_account_payment(): void
    {
        $this->fakeMidtrans();
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $this->actingAs($user);

        $res = $this->postJson(route('payment.va.create', $campaign), [
            'amount' => 25000,
            'bank' => 'bca',
        ]);

        $res->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('payment.payment_method', 'virtual_account')
            ->assertJsonPath('virtual_account_number', '1234567890');
    }

    public function test_get_status_updates_payment_from_midtrans(): void
    {
        $this->fakeMidtrans();
        $payment = Payment::factory()->create([
            'midtrans_order_id' => 'ORDER-123',
            'status' => 'pending',
            'transaction_status' => 'pending',
        ]);

        $res = $this->getJson(route('payment.status', $payment));
        $res->assertOk()->assertJsonPath('payment.status', 'paid');
        $this->assertEquals('paid', $payment->fresh()->status);
        $this->assertEquals('settlement', $payment->fresh()->transaction_status);
    }

    public function test_success_and_failed_pages_are_accessible(): void
    {
        $this->fakeMidtrans();
        $payment = Payment::factory()->create();
        $this->get(route('payment.success', $payment))->assertOk()->assertViewIs('payment.success');
        $this->get(route('payment.failed', $payment))->assertOk()->assertViewIs('payment.failed');
    }
}


