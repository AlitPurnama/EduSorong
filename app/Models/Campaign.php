<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_verification_id',
        'title',
        'location',
        'organization',
        'image_path',
        'target_amount',
        'raised_amount',
        'excerpt',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    public function organizationVerification()
    {
        return $this->belongsTo(OrganizationVerification::class);
    }

    public function updates()
    {
        return $this->hasMany(CampaignUpdate::class)->latest();
    }

    public function deletionRequests()
    {
        return $this->hasMany(CampaignDeletionRequest::class);
    }

    /**
     * Get total withdrawn amount (from completed withdrawals)
     */
    public function getTotalWithdrawnAttribute(): int
    {
        return $this->withdrawalRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('requested_amount');
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute(): int
    {
        return max(0, $this->raised_amount - $this->total_withdrawn);
    }

    /**
     * Get organization name (from verification or manual input)
     */
    public function getOrganizationNameAttribute(): string
    {
        if ($this->organizationVerification && $this->organizationVerification->isApproved()) {
            return $this->organizationVerification->organization_name;
        }
        
        return $this->organization ?? 'Perorangan';
    }

    /**
     * Check if campaign organization is verified
     */
    public function hasVerifiedOrganization(): bool
    {
        return $this->organizationVerification && $this->organizationVerification->isApproved();
    }

    /**
     * Check if campaign can request withdrawal
     * Can request if raised_amount >= 80% of target_amount
     */
    public function canRequestWithdrawal(): bool
    {
        if ($this->target_amount == 0) {
            return false;
        }
        
        $percentage = ($this->raised_amount / $this->target_amount) * 100;
        return $percentage >= 80;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }
        
        return min(100, ($this->raised_amount / $this->target_amount) * 100);
    }
}


