<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CampaignUpdateController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        // Ensure user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        $data = [
            'campaign_id' => $campaign->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('campaign-updates', 'public');
        }

        CampaignUpdate::create($data);

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Update kampanye berhasil ditambahkan.');
    }

    public function destroy(Campaign $campaign, CampaignUpdate $update)
    {
        // Ensure user owns the campaign
        if ($campaign->user_id !== Auth::id() || $update->campaign_id !== $campaign->id) {
            abort(403, 'Unauthorized.');
        }

        // Delete image if exists
        if ($update->image_path) {
            Storage::disk('public')->delete($update->image_path);
        }

        $update->delete();

        return redirect()->route('campaigns.show', $campaign)
            ->with('success', 'Update berhasil dihapus.');
    }
}

