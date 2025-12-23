<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    /**
     * Hanya pemilik kampanye yang boleh menghapus.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        return $campaign->user_id === $user->id;
    }
}


