<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\WithdrawalRequest;
use App\Models\WithdrawalEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WithdrawalEvidenceController extends Controller
{
    public function create(WithdrawalRequest $withdrawal)
    {
        // Ensure user owns the withdrawal request
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // Only allow upload if withdrawal is completed
        if ($withdrawal->status !== 'completed') {
            return redirect()->route('withdrawal.show', $withdrawal)
                ->with('error', 'Bukti penggunaan hanya bisa diupload setelah pencairan dana selesai.');
        }

        return view('withdrawal.evidence.create', compact('withdrawal'));
    }

    public function store(Request $request, WithdrawalRequest $withdrawal)
    {
        // Ensure user owns the withdrawal request
        if ($withdrawal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // Only allow upload if withdrawal is completed
        if ($withdrawal->status !== 'completed') {
            return redirect()->route('withdrawal.show', $withdrawal)
                ->with('error', 'Bukti penggunaan hanya bisa diupload setelah pencairan dana selesai.');
        }

        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:1000'],
            'evidence' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
            'used_at' => ['nullable', 'date'],
        ]);

        $evidencePath = $request->file('evidence')->store('withdrawal-evidences', 'public');

        WithdrawalEvidence::create([
            'withdrawal_request_id' => $withdrawal->id,
            'description' => $validated['description'],
            'evidence_path' => $evidencePath,
            'used_at' => $validated['used_at'] ? date('Y-m-d', strtotime($validated['used_at'])) : now(),
            'status' => 'pending',
        ]);

        // Mark evidence as uploaded
        $withdrawal->update(['evidence_uploaded' => true]);

        return redirect()->route('withdrawal.show', $withdrawal)
            ->with('success', 'Bukti penggunaan berhasil diupload. Menunggu verifikasi admin.');
    }

    public function destroy(WithdrawalRequest $withdrawal, WithdrawalEvidence $evidence)
    {
        // Ensure user owns the withdrawal request
        if ($withdrawal->user_id !== Auth::id() || $evidence->withdrawal_request_id !== $withdrawal->id) {
            abort(403, 'Unauthorized.');
        }

        // Only allow delete if evidence is pending or rejected
        if ($evidence->isVerified()) {
            return redirect()->route('withdrawal.show', $withdrawal)
                ->with('error', 'Bukti yang sudah terverifikasi tidak bisa dihapus.');
        }

        // Delete file
        if ($evidence->evidence_path) {
            Storage::disk('public')->delete($evidence->evidence_path);
        }

        $evidence->delete();

        // Update evidence_uploaded status if no evidences left
        if ($withdrawal->evidences()->count() === 0) {
            $withdrawal->update(['evidence_uploaded' => false]);
        }

        return redirect()->route('withdrawal.show', $withdrawal)
            ->with('success', 'Bukti penggunaan berhasil dihapus.');
    }
}

