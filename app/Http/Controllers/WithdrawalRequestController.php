<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalRequestController extends Controller
{
    public function create(Campaign $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // Check if campaign can request withdrawal
        if (!$campaign->canRequestWithdrawal()) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Kampanye belum mencapai minimal 80% dari target untuk bisa melakukan pencairan dana.');
        }

        // Check if there's already a pending request
        $existingRequest = WithdrawalRequest::where('campaign_id', $campaign->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRequest) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Anda sudah memiliki request pencairan dana yang sedang diproses.');
        }

        return view('withdrawal.create', compact('campaign'));
    }

    public function store(Request $request, Campaign $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // Check if campaign can request withdrawal
        if (!$campaign->canRequestWithdrawal()) {
            return redirect()->route('campaigns.show', $campaign)
                ->with('error', 'Kampanye belum mencapai minimal 80% dari target untuk bisa melakukan pencairan dana.');
        }

        $validated = $request->validate([
            'requested_amount' => ['required', 'integer', 'min:10000', 'max:' . $campaign->raised_amount],
            'purpose' => ['required', 'string', 'max:1000'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:255'],
        ]);

        WithdrawalRequest::create([
            'campaign_id' => $campaign->id,
            'user_id' => Auth::id(),
            'requested_amount' => $validated['requested_amount'],
            'purpose' => $validated['purpose'],
            'bank_name' => $validated['bank_name'],
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_name' => $validated['bank_account_name'],
            'status' => 'pending',
        ]);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Request pencairan dana berhasil dikirim. Admin akan meninjau request Anda.');
    }

    public function show(WithdrawalRequest $withdrawal)
    {
        // Check if user owns the withdrawal request
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $withdrawal->load(['campaign', 'evidences.verifier']);

        return view('withdrawal.show', compact('withdrawal'));
    }
}

