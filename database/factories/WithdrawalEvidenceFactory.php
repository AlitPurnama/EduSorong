<?php

namespace Database\Factories;

use App\Models\WithdrawalEvidence;
use App\Models\WithdrawalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\WithdrawalEvidence>
 */
class WithdrawalEvidenceFactory extends Factory
{
    protected $model = WithdrawalEvidence::class;

    public function definition(): array
    {
        return [
            'withdrawal_request_id' => WithdrawalRequest::factory(),
            'description' => $this->faker->sentence(8),
            'evidence_path' => 'withdrawal-evidences/' . $this->faker->uuid . '.jpg',
            'status' => 'pending',
            'rejection_reason' => null,
            'verified_by' => null,
            'verified_at' => null,
            'used_at' => now(),
        ];
    }
}


