<?php

namespace Tests\Unit;

use App\Models\WithdrawalEvidence;
use App\Models\WithdrawalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalRequestModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_uploaded_evidence_and_all_verified(): void
    {
        $wr = WithdrawalRequest::factory()->create([
            'evidence_uploaded' => false,
        ]);
        $this->assertFalse($wr->hasUploadedEvidence());
        $this->assertFalse($wr->allEvidencesVerified());

        // Add pending evidence
        WithdrawalEvidence::factory()->create(['withdrawal_request_id' => $wr->id, 'status' => 'pending']);
        $wr->update(['evidence_uploaded' => true]);
        $this->assertTrue($wr->fresh()->hasUploadedEvidence());
        $this->assertFalse($wr->fresh()->allEvidencesVerified());

        // Add verified evidence and mark previous as verified
        $wr->evidences()->update(['status' => 'verified']);
        $this->assertTrue($wr->fresh()->allEvidencesVerified());
    }
}


