<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\CampaignUpdate>
 */
class CampaignUpdateFactory extends Factory
{
    protected $model = CampaignUpdate::class;

    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraph(),
            'image_path' => null,
        ];
    }
}


