<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\WithdrawalRequest>
 */
class WithdrawalRequestFactory extends Factory
{
    protected $model = WithdrawalRequest::class;

    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
            'requested_amount' => $this->faker->numberBetween(10000, 200000),
            'purpose' => $this->faker->sentence(8),
            'bank_name' => 'BCA',
            'bank_account_number' => $this->faker->bankAccountNumber(),
            'bank_account_name' => $this->faker->name(),
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'completed_at' => null,
        ];
    }

    public function approved(): self
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'reviewed_at' => now(),
            'completed_at' => now(),
        ]);
    }
}


