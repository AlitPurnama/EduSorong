<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignDeletionRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\CampaignDeletionRequest>
 */
class CampaignDeletionRequestFactory extends Factory
{
    protected $model = CampaignDeletionRequest::class;

    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
            'reason' => $this->faker->sentence(10),
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}


