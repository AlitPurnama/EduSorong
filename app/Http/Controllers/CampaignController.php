<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignRequest;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(6);

        return view('campaigns.index', compact('campaigns'));
    }

    public function show(Campaign $campaign)
    {
        return view('campaigns.show', compact('campaign'));
    }

    public function dashboard()
    {
        $campaigns = Campaign::where('user_id', Auth::id())->latest()->get();

        return view('dashboard.index', compact('campaigns'));
    }

    public function create()
    {
        return view('dashboard.campaigns.create');
    }

    public function store(StoreCampaignRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('campaigns', 'public');
        }

        Campaign::create($data + ['raised_amount' => 0]);

        return redirect()->route('dashboard')->with('status', 'Kampanye berhasil dibuat.');
    }

    public function destroy(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return redirect()->route('dashboard')->with('status', 'Kampanye dihapus.');
    }
}


