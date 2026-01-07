<?php

namespace App\Http\Controllers;

use App\Models\OrganizationVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrganizationVerificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $verifications = $user->organizationVerifications()->latest()->get();
        $ktpVerified = $user->isKtpVerified();
        
        return view('organization.index', compact('user', 'verifications', 'ktpVerified'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Check if user has verified KTP
        if ($user->ktp_verification_status !== 'approved') {
            return redirect()->route('organization.index')
                ->with('error', 'Anda harus verifikasi KTP terlebih dahulu sebelum dapat mengajukan verifikasi organisasi/yayasan. Silakan verifikasi KTP di halaman Pengaturan.');
        }
        
        // Check if user already has 3 approved organizations
        $approvedCount = $user->organizationVerifications()
            ->where('status', 'approved')
            ->count();
            
        if ($approvedCount >= 3) {
            return redirect()->route('organization.index')
                ->with('error', 'Anda sudah mencapai batas maksimal 3 organisasi terverifikasi.');
        }

        // Check if there's already a pending verification
        $pendingCount = $user->organizationVerifications()
            ->where('status', 'pending')
            ->count();
            
        if ($pendingCount > 0) {
            return redirect()->route('organization.index')
                ->with('error', 'Anda memiliki verifikasi organisasi yang sedang diproses. Tunggu hingga verifikasi selesai sebelum mengajukan yang baru.');
        }

        return view('organization.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user has verified KTP
        if ($user->ktp_verification_status !== 'approved') {
            return redirect()->route('organization.index')
                ->with('error', 'Anda harus verifikasi KTP terlebih dahulu sebelum dapat mengajukan verifikasi organisasi/yayasan.');
        }

        // Check if user already has 3 approved organizations
        $approvedCount = $user->organizationVerifications()
            ->where('status', 'approved')
            ->count();
            
        if ($approvedCount >= 3) {
            return redirect()->route('organization.index')
                ->with('error', 'Anda sudah mencapai batas maksimal 3 organisasi terverifikasi.');
        }

        // Check if there's already a pending verification
        $pendingCount = $user->organizationVerifications()
            ->where('status', 'pending')
            ->count();
            
        if ($pendingCount > 0) {
            return redirect()->route('organization.index')
                ->with('error', 'Anda memiliki verifikasi organisasi yang sedang diproses.');
        }

        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_description' => ['nullable', 'string', 'max:1000'],
            'npwp' => ['required', 'string', 'max:20'],
            'phone' => ['required', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
        ]);

        // Store document
        $documentPath = $request->file('document')->store('organization-documents', 'public');

        OrganizationVerification::create([
            'user_id' => $user->id,
            'organization_name' => $validated['organization_name'],
            'organization_description' => $validated['organization_description'],
            'npwp' => $validated['npwp'],
            'phone' => $validated['phone'],
            'website' => $validated['website'],
            'address' => $validated['address'],
            'document_path' => $documentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('organization.index')
            ->with('success', 'Verifikasi organisasi berhasil dikirim. Admin akan meninjau verifikasi Anda.');
    }

    public function show(OrganizationVerification $organizationVerification)
    {
        // Ensure user owns this verification
        if ($organizationVerification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        return view('organization.show', compact('organizationVerification'));
    }
}

