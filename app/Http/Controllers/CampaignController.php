<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index()
    {
        $query = Campaign::with(['user', 'organizationVerification']);

        // Search by title or location
        if (request()->has('search') && request()->filled('search')) {
            $search = request()->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $campaigns = $query->latest()->paginate(6)->withQueryString();

        return view('campaigns.index', compact('campaigns'));
    }

    public function show(Campaign $campaign)
    {
        $campaign->load([
            'user', 
            'organizationVerification',
            'payments' => function($query) {
                $query->whereIn('status', ['paid', 'settlement'])
                    ->latest()
                    ->limit(50); // Limit untuk performa
            },
            'payments.user',
            'withdrawalRequests' => function($query) {
                $query->whereIn('status', ['approved', 'completed'])
                    ->latest();
            },
            'withdrawalRequests.evidences' => function($query) {
                $query->latest();
            },
            'updates' => function($query) {
                $query->latest()->limit(10);
            },
            'updates.user'
        ]);
        
        return view('campaigns.show', compact('campaign'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $campaigns = Campaign::where('user_id', Auth::id())
            ->with('deletionRequests')
            ->latest()
            ->get();

        return view('dashboard.index', compact('campaigns', 'user'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Check if user has verified KTP
        if ($user->ktp_verification_status !== 'approved') {
            return redirect()->route('dashboard')
                ->with('error', 'Anda harus verifikasi KTP terlebih dahulu sebelum dapat membuat kampanye. Silakan verifikasi KTP di halaman Pengaturan.');
        }

        // Get user's approved organizations
        $approvedOrganizations = \App\Models\OrganizationVerification::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->get();
            
        return view('dashboard.campaigns.create', compact('approvedOrganizations'));
    }

    public function store(StoreCampaignRequest $request)
    {
        $user = Auth::user();
        
        // Check if user has verified KTP
        if ($user->ktp_verification_status !== 'approved') {
            return redirect()->route('dashboard')
                ->with('error', 'Anda harus verifikasi KTP terlebih dahulu sebelum dapat membuat kampanye. Silakan verifikasi KTP di halaman Pengaturan.');
        }

        $data = $request->validated();

        $data['user_id'] = Auth::id();

        // If organization_verification_id is selected, verify ownership
        if ($request->filled('organization_verification_id')) {
            $orgVerification = \App\Models\OrganizationVerification::findOrFail($request->organization_verification_id);
            
            // Ensure the organization belongs to the user and is approved
            if ($orgVerification->user_id !== Auth::id()) {
                return back()->withErrors(['organization_verification_id' => 'Organisasi tidak valid.']);
            }
            
            if (!$orgVerification->isApproved()) {
                return back()->withErrors(['organization_verification_id' => 'Organisasi belum terverifikasi.']);
            }
            
            // Set organization name from verification
            $data['organization'] = $orgVerification->organization_name;
        } else {
            // If no organization selected, set to null (will show as "Perorangan")
            $data['organization'] = $request->organization ?? null;
            $data['organization_verification_id'] = null;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('campaigns', 'public');
        }

        Campaign::create($data + ['raised_amount' => 0]);

        return redirect()->route('dashboard')->with('status', 'Kampanye berhasil dibuat.');
    }

    public function destroy(Request $request, Campaign $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // Check if campaign has donations
        if ($campaign->raised_amount > 0) {
            // Campaign has donations, need admin review
            $validated = $request->validate([
                'reason' => ['required', 'string', 'max:1000'],
            ]);

            // Check if there's already a pending deletion request
            $existingRequest = \App\Models\CampaignDeletionRequest::where('campaign_id', $campaign->id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return redirect()->route('dashboard')
                    ->with('error', 'Anda sudah memiliki request penghapusan yang sedang diproses.');
            }

            // Create deletion request
            \App\Models\CampaignDeletionRequest::create([
                'campaign_id' => $campaign->id,
                'user_id' => Auth::id(),
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            return redirect()->route('dashboard')
                ->with('status', 'Request penghapusan kampanye telah dikirim. Admin akan meninjau request Anda.');
        }

        // No donations, delete immediately
        $campaign->delete();

        return redirect()->route('dashboard')->with('status', 'Kampanye dihapus.');
    }

    public function requestDeletion(Campaign $campaign)
    {
        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        // If campaign has no donations, delete immediately
        if ($campaign->raised_amount == 0) {
            $campaign->delete();
            return redirect()->route('dashboard')
                ->with('status', 'Kampanye berhasil dihapus.');
        }

        // Check if there's already a pending deletion request
        $existingRequest = \App\Models\CampaignDeletionRequest::where('campaign_id', $campaign->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda sudah memiliki request penghapusan yang sedang diproses.');
        }

        return view('campaigns.request-deletion', compact('campaign'));
    }
}


