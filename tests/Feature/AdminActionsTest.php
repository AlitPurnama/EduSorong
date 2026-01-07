<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignDeletionRequest;
use App\Models\OrganizationVerification;
use App\Models\User;
use App\Models\WithdrawalEvidence;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActionsTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'ktp_verification_status' => 'approved',
            'ktp_verified' => true,
        ]);
    }

    public function test_admin_can_approve_and_reject_org_verification(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $ov = OrganizationVerification::factory()->create();

        $this->post(route('admin.verifications.approve', $ov))
            ->assertRedirect(route('admin.verifications'))
            ->assertSessionHas('success');
        $this->assertEquals('approved', $ov->fresh()->status);

        $ov2 = OrganizationVerification::factory()->create();
        $this->post(route('admin.verifications.reject', $ov2), [
            'rejection_reason' => 'Dokumen tidak valid',
        ])->assertRedirect(route('admin.verifications'))
          ->assertSessionHas('success');
        $this->assertEquals('rejected', $ov2->fresh()->status);
    }

    public function test_admin_can_approve_reject_complete_withdrawal(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $campaign = Campaign::factory()->create(['raised_amount' => 100000]);
        $wr = WithdrawalRequest::factory()->create([
            'campaign_id' => $campaign->id,
            'requested_amount' => 50000,
        ]);

        // Approve
        $this->post(route('admin.withdrawals.approve', $wr))
            ->assertRedirect(route('admin.withdrawals'))
            ->assertSessionHas('success');
        $wr->refresh();
        $campaign->refresh();
        $this->assertEquals('approved', $wr->status);
        $this->assertEquals(50000, $campaign->raised_amount);

        // Complete
        $this->post(route('admin.withdrawals.complete', $wr))
            ->assertRedirect(route('admin.withdrawals'))
            ->assertSessionHas('success');
        $this->assertEquals('completed', $wr->fresh()->status);

        // Reject another WR
        $wr2 = WithdrawalRequest::factory()->create(['campaign_id' => $campaign->id]);
        $this->post(route('admin.withdrawals.reject', $wr2), [
            'rejection_reason' => 'Tidak memenuhi syarat',
        ])->assertRedirect(route('admin.withdrawals'))
          ->assertSessionHas('success');
        $this->assertEquals('rejected', $wr2->fresh()->status);
    }

    public function test_admin_can_verify_and_reject_evidence(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $wr = WithdrawalRequest::factory()->completed()->create();
        $evidence = \App\Models\WithdrawalEvidence::factory()->create([
            'withdrawal_request_id' => $wr->id,
            'status' => 'pending',
        ]);

        $this->post(route('admin.evidences.verify', $evidence), ['action' => 'approve'])
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertEquals('verified', $evidence->fresh()->status);

        $evidence2 = \App\Models\WithdrawalEvidence::factory()->create([
            'withdrawal_request_id' => $wr->id,
            'status' => 'pending',
        ]);
        $this->post(route('admin.evidences.verify', $evidence2), [
            'action' => 'reject',
            'rejection_reason' => 'Bukti tidak jelas',
        ])->assertRedirect()
          ->assertSessionHas('success');
        $this->assertEquals('rejected', $evidence2->fresh()->status);
    }

    public function test_admin_can_approve_and_reject_campaign_deletion_request(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $campaign = Campaign::factory()->create();
        $cdr = CampaignDeletionRequest::factory()->create([
            'campaign_id' => $campaign->id,
        ]);

        // Approve -> campaign deleted
        $this->post(route('admin.deletion-requests.approve', $cdr))
            ->assertRedirect(route('admin.deletion-requests'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
        // Deleting campaign cascades deleting the deletion request, ensure it's gone
        $this->assertDatabaseMissing('campaign_deletion_requests', ['id' => $cdr->id]);

        // Reject another request
        $campaign2 = Campaign::factory()->create();
        $cdr2 = CampaignDeletionRequest::factory()->create([
            'campaign_id' => $campaign2->id,
        ]);
        $this->post(route('admin.deletion-requests.reject', $cdr2), [
            'rejection_reason' => 'Alasan kurang kuat',
        ])->assertRedirect(route('admin.deletion-requests'))
          ->assertSessionHas('success');
        $this->assertEquals('rejected', $cdr2->fresh()->status);
        $this->assertDatabaseHas('campaigns', ['id' => $campaign2->id]);
    }

    public function test_admin_can_approve_and_reject_ktp_verification(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $user = User::factory()->create([
            'ktp_verification_status' => 'pending',
            'ktp_verified' => false,
            'ktp_photo' => 'ktp/sample.jpg',
        ]);

        $this->post(route('admin.ktp-verifications.approve', $user))
            ->assertRedirect(route('admin.ktp-verifications'))
            ->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('approved', $user->ktp_verification_status);
        $this->assertTrue((bool) $user->ktp_verified);

        $user2 = User::factory()->create([
            'ktp_verification_status' => 'pending',
            'ktp_verified' => false,
            'ktp_photo' => 'ktp/sample2.jpg',
        ]);
        $this->post(route('admin.ktp-verifications.reject', $user2), [
            'rejection_reason' => 'Tidak terbaca',
        ])->assertRedirect(route('admin.ktp-verifications'))
          ->assertSessionHas('success');
        $this->assertEquals('rejected', $user2->fresh()->ktp_verification_status);
        $this->assertFalse((bool) $user2->fresh()->ktp_verified);
    }
}


