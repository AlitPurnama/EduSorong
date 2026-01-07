<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WithdrawalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function verifiedUser(): User
    {
        return User::factory()->create([
            'ktp_verification_status' => 'approved',
            'ktp_verified' => true,
        ]);
    }

    public function test_user_can_open_withdrawal_create_when_campaign_meets_threshold(): void
    {
        $user = $this->verifiedUser();
        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 100000,
            'raised_amount' => 80000, // 80%
        ]);

        $this->actingAs($user);
        $this->get(route('withdrawal.create', $campaign))
            ->assertOk()
            ->assertViewIs('withdrawal.create');
    }

    public function test_user_can_store_withdrawal_request(): void
    {
        $user = $this->verifiedUser();
        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 100000,
            'raised_amount' => 90000,
        ]);

        $this->actingAs($user);
        $response = $this->post(route('withdrawal.store', $campaign), [
            'requested_amount' => 50000,
            'purpose' => 'Biaya operasional',
            'bank_name' => 'BCA',
            'bank_account_number' => '0123456789',
            'bank_account_name' => 'Nama Rekening',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('withdrawal_requests', [
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'requested_amount' => 50000,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_upload_evidence_only_after_completed(): void
    {
        Storage::fake('public');
        $user = $this->verifiedUser();
        $this->actingAs($user);
        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 100000,
            'raised_amount' => 90000,
        ]);
        $withdrawal = WithdrawalRequest::factory()->completed()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'requested_amount' => 50000,
        ]);

        // Can open form
        $this->get(route('withdrawal.evidence.create', $withdrawal))
            ->assertOk()
            ->assertViewIs('withdrawal.evidence.create');

        // Can upload evidence
        $response = $this->post(route('withdrawal.evidence.store', $withdrawal), [
            'description' => 'Kuitansi pembelian',
            'evidence' => UploadedFile::fake()->image('evidence.jpg'),
            'used_at' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('withdrawal.show', $withdrawal));
        $response->assertSessionHas('success');

        $withdrawal->refresh();
        $this->assertTrue((bool) $withdrawal->evidence_uploaded);
        $this->assertDatabaseHas('withdrawal_evidences', [
            'withdrawal_request_id' => $withdrawal->id,
            'status' => 'pending',
        ]);
    }

    public function test_upload_evidence_is_blocked_if_withdrawal_not_completed(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);
        $withdrawal = WithdrawalRequest::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
        ]);

        $this->get(route('withdrawal.evidence.create', $withdrawal))
            ->assertRedirect(route('withdrawal.show', $withdrawal))
            ->assertSessionHas('error');
    }
}


