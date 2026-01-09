<?php

namespace App\Http\Controllers;

use App\Mail\KtpVerificationApprovedMail;
use App\Mail\KtpVerificationRejectedMail;
use App\Models\Campaign;
use App\Models\OrganizationVerification;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'pending_verifications' => OrganizationVerification::where('status', 'pending')->count(),
            'pending_ktp_verifications' => \App\Models\User::where('ktp_verification_status', 'pending')->count(),
            'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->count(),
            'pending_deletion_requests' => \App\Models\CampaignDeletionRequest::where('status', 'pending')->count(),
            'total_campaigns' => Campaign::count(),
            'total_users' => \App\Models\User::where('role', 'user')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // Organization Verifications
    public function verifications()
    {
        $verifications = OrganizationVerification::with(['user', 'verifier'])
            ->latest()
            ->paginate(15);

        return view('admin.verifications', compact('verifications'));
    }

    public function approveVerification(OrganizationVerification $verification)
    {
        DB::transaction(function () use ($verification) {
            $verification->update([
                'status' => 'approved',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);
        });

        return redirect()->route('admin.verifications')
            ->with('success', 'Verifikasi organisasi/yayasan berhasil disetujui.');
    }

    public function rejectVerification(Request $request, OrganizationVerification $verification)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($verification, $request) {
            $verification->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);
        });

        return redirect()->route('admin.verifications')
            ->with('success', 'Verifikasi organisasi/yayasan ditolak.');
    }

    // Withdrawal Requests
    public function withdrawals()
    {
        $withdrawals = WithdrawalRequest::with(['campaign.user', 'user', 'reviewer', 'evidences.verifier'])
            ->latest()
            ->paginate(15);

        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function approveWithdrawal(WithdrawalRequest $withdrawal)
    {
        // Check if campaign has enough funds (use remaining_balance, not raised_amount)
        if ($withdrawal->campaign->remaining_balance < $withdrawal->requested_amount) {
            return redirect()->route('admin.withdrawals')
                ->with('error', 'Dana kampanye tidak mencukupi untuk pencairan ini.');
        }

        DB::transaction(function () use ($withdrawal) {
            $withdrawal->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.withdrawals')
            ->with('success', 'Request pencairan dana disetujui.');
    }

    public function rejectWithdrawal(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($withdrawal, $request) {
            $withdrawal->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.withdrawals')
            ->with('success', 'Request pencairan dana ditolak.');
    }

    public function completeWithdrawal(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'approved') {
            return redirect()->route('admin.withdrawals')
                ->with('error', 'Hanya request yang sudah disetujui yang bisa diselesaikan.');
        }

        $withdrawal->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('admin.withdrawals')
            ->with('success', 'Pencairan dana ditandai sebagai selesai.');
    }

    public function verifyEvidence(Request $request, \App\Models\WithdrawalEvidence $evidence)
    {
        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'approve') {
            DB::transaction(function () use ($evidence) {
                $evidence->update([
                    'status' => 'verified',
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                ]);
            });

            return redirect()->back()
                ->with('success', 'Bukti penggunaan disetujui.');
        } elseif ($action === 'reject') {
            $validated = $request->validate([
                'rejection_reason' => ['required', 'string', 'max:500'],
            ]);

            DB::transaction(function () use ($evidence, $validated) {
                $evidence->update([
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                ]);
            });

            return redirect()->back()
                ->with('success', 'Bukti penggunaan ditolak.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    // Campaign Deletion Requests
    public function deletionRequests()
    {
        $deletionRequests = \App\Models\CampaignDeletionRequest::with(['campaign.user', 'user', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('admin.deletion-requests', compact('deletionRequests'));
    }

    public function approveDeletionRequest(\App\Models\CampaignDeletionRequest $deletionRequest)
    {
        if ($deletionRequest->status !== 'pending') {
            return redirect()->route('admin.deletion-requests')
                ->with('error', 'Hanya request yang pending yang bisa disetujui.');
        }

        DB::transaction(function () use ($deletionRequest) {
            $deletionRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Delete the campaign
            $deletionRequest->campaign->delete();
        });

        return redirect()->route('admin.deletion-requests')
            ->with('success', 'Request penghapusan kampanye disetujui dan kampanye telah dihapus.');
    }

    public function rejectDeletionRequest(Request $request, \App\Models\CampaignDeletionRequest $deletionRequest)
    {
        if ($deletionRequest->status !== 'pending') {
            return redirect()->route('admin.deletion-requests')
                ->with('error', 'Hanya request yang pending yang bisa ditolak.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($deletionRequest, $validated) {
            $deletionRequest->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.deletion-requests')
            ->with('success', 'Request penghapusan kampanye ditolak.');
    }

    // KTP Verifications
    public function ktpVerifications()
    {
        $users = \App\Models\User::whereIn('ktp_verification_status', ['pending', 'approved', 'rejected'])
            ->whereNotNull('ktp_photo')
            ->with('campaigns')
            ->latest('updated_at')
            ->paginate(15);

        return view('admin.ktp-verifications', compact('users'));
    }

    public function approveKtpVerification(\App\Models\User $user)
    {
        if ($user->ktp_verification_status !== 'pending') {
            return redirect()->route('admin.ktp-verifications')
                ->with('error', 'Hanya verifikasi yang pending yang bisa disetujui.');
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'ktp_verification_status' => 'approved',
                'ktp_verified' => true,
            ]);
        });

        // Send approval email
        try {
            \Log::info('Attempting to send KTP approval email to: ' . $user->email);
            Mail::to($user->email)->send(new KtpVerificationApprovedMail($user));
            \Log::info('KTP approval email sent successfully to: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send KTP approval email to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

        return redirect()->route('admin.ktp-verifications')
            ->with('success', 'Verifikasi KTP berhasil disetujui.');
    }

    public function rejectKtpVerification(Request $request, \App\Models\User $user)
    {
        if ($user->ktp_verification_status !== 'pending') {
            return redirect()->route('admin.ktp-verifications')
                ->with('error', 'Hanya verifikasi yang pending yang bisa ditolak.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($user, $request) {
            $user->update([
                'ktp_verification_status' => 'rejected',
                'ktp_rejection_reason' => $request->rejection_reason,
                'ktp_verified' => false,
            ]);
        });

        // Send rejection email
        try {
            \Log::info('Attempting to send KTP rejection email to: ' . $user->email);
            Mail::to($user->email)->send(new KtpVerificationRejectedMail($user, $request->rejection_reason));
            \Log::info('KTP rejection email sent successfully to: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send KTP rejection email to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

        return redirect()->route('admin.ktp-verifications')
            ->with('success', 'Verifikasi KTP ditolak.');
    }
}

