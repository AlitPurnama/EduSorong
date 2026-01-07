<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'requested_amount',
        'purpose',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'requires_evidence',
        'evidence_uploaded',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'completed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Format requested amount as currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->requested_amount, 0, ',', '.');
    }

    public function evidences()
    {
        return $this->hasMany(WithdrawalEvidence::class);
    }

    /**
     * Check if evidence is required and uploaded
     */
    public function hasUploadedEvidence(): bool
    {
        return $this->evidence_uploaded && $this->evidences()->exists();
    }

    /**
     * Check if all evidences are verified
     */
    public function allEvidencesVerified(): bool
    {
        if ($this->evidences()->count() === 0) {
            return false;
        }
        
        return $this->evidences()->where('status', '!=', 'verified')->count() === 0;
    }
}

