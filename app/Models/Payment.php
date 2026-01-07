<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'donor_name',
        'donor_phone',
        'donor_email',
        'donor_message',
        'is_anonymous',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'payment_method',
        'payment_channel',
        'status',
        'transaction_status',
        'amount',
        'currency',
        'reference_id',
        'qr_string',
        'qr_url',
        'virtual_account_number',
        'deeplink_url',
        'notification_data',
        'expires_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'is_anonymous' => 'boolean',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'notification_data' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' || $this->transaction_status === 'settlement';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               $this->transaction_status === 'expire' ||
               ($this->expires_at && $this->expires_at->isPast() && $this->status !== 'paid');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' || $this->transaction_status === 'pending';
    }

    /**
     * Get donor display name (masked if anonymous)
     */
    public function getDonorDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return $this->maskName($this->donor_name ?? 'Anonim');
        }
        
        return $this->donor_name ?? ($this->user ? $this->user->name : 'Anonim');
    }

    /**
     * Mask name for anonymous display
     */
    protected function maskName(string $name): string
    {
        if (strlen($name) <= 2) {
            return str_repeat('*', strlen($name));
        }
        
        $words = explode(' ', $name);
        $masked = [];
        
        foreach ($words as $word) {
            if (strlen($word) <= 2) {
                $masked[] = str_repeat('*', strlen($word));
            } else {
                $firstChar = substr($word, 0, 1);
                $lastChar = substr($word, -1, 1);
                $middle = str_repeat('*', strlen($word) - 2);
                $masked[] = $firstChar . $middle . $lastChar;
            }
        }
        
        return implode(' ', $masked);
    }
}

