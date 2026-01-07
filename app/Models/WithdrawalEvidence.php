<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdrawal_request_id',
        'description',
        'evidence_path',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'used_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function withdrawalRequest()
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get evidence URL
     */
    public function getEvidenceUrlAttribute(): string
    {
        return asset('storage/' . $this->evidence_path);
    }
}

