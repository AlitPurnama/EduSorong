<?php

namespace Tests\Unit;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_helpers(): void
    {
        $p = Payment::factory()->create(['status' => 'pending', 'transaction_status' => 'pending']);
        $this->assertTrue($p->isPending());
        $this->assertFalse($p->isPaid());

        $p->update(['status' => 'paid', 'transaction_status' => 'settlement']);
        $this->assertTrue($p->fresh()->isPaid());

        $p->update(['status' => 'expired', 'transaction_status' => 'expire']);
        $this->assertTrue($p->fresh()->isExpired());
    }

    public function test_donor_display_name_masking(): void
    {
        $p = Payment::factory()->create([
            'is_anonymous' => true,
            'donor_name' => 'John Doe',
        ]);
        $masked = $p->donor_display_name;
        $this->assertNotEquals('John Doe', $masked);
        $this->assertStringContainsString('*', $masked);
    }
}


