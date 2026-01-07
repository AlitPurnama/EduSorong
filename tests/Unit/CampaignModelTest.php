<?php

namespace Tests\Unit;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_request_withdrawal_logic(): void
    {
        $campaign = Campaign::factory()->create([
            'target_amount' => 0,
            'raised_amount' => 0,
        ]);
        $this->assertFalse($campaign->canRequestWithdrawal());

        $campaign->update(['target_amount' => 100000, 'raised_amount' => 70000]);
        $this->assertFalse($campaign->fresh()->canRequestWithdrawal());

        $campaign->update(['raised_amount' => 80000]);
        $this->assertTrue($campaign->fresh()->canRequestWithdrawal());
    }

    public function test_remaining_balance_and_progress_percentage(): void
    {
        $campaign = Campaign::factory()->create([
            'target_amount' => 200000,
            'raised_amount' => 150000,
        ]);
        $this->assertEquals(150000, $campaign->remaining_balance);
        $this->assertEquals(75.0, $campaign->progress_percentage);

        $campaign->update(['raised_amount' => 300000]);
        $this->assertEquals(100.0, $campaign->fresh()->progress_percentage);
    }
}


