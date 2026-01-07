<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'location' => $this->faker->city(),
            'organization' => null,
            'image_path' => null,
            'target_amount' => $this->faker->numberBetween(1_000_000, 10_000_000),
            'raised_amount' => 0,
            'excerpt' => $this->faker->sentence(8),
            'description' => $this->faker->paragraph(),
        ];
    }
}


