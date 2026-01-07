<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DonationFeedController extends Controller
{
    /**
     * Get recent successful donations for running text
     */
    public function getRecentDonations(Request $request)
    {
        // Cache for 30 seconds to optimize performance
        $cacheKey = 'recent_donations_feed';
        $limit = $request->input('limit', 10);
        
        $donations = Cache::remember($cacheKey, 30, function () use ($limit) {
            return Payment::where(function ($query) {
                    $query->where('status', 'paid')
                          ->orWhere('transaction_status', 'settlement');
                })
                ->whereNotNull('paid_at')
                ->with(['campaign:id,title'])
                ->orderBy('paid_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'donor_name' => $payment->donor_display_name, // Uses accessor with masking
                        'campaign_title' => $payment->campaign->title ?? 'Kampanye',
                        'amount' => $payment->amount,
                        'paid_at' => $payment->paid_at?->diffForHumans(),
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'donations' => $donations,
        ]);
    }
}

