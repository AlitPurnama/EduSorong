<?php

namespace Database\Factories;

use App\Models\OrganizationVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\OrganizationVerification>
 */
class OrganizationVerificationFactory extends Factory
{
    protected $model = OrganizationVerification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organization_name' => $this->faker->company(),
            'organization_description' => $this->faker->sentence(10),
            'npwp' => $this->faker->numerify('################'),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'address' => $this->faker->address(),
            'document_path' => 'organization-documents/' . $this->faker->uuid . '.pdf',
            'status' => 'pending',
            'rejection_reason' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];
    }

    public function approved(): self
    {
        return $this->state(function () {
            return [
                'status' => 'approved',
                'verified_at' => now(),
            ];
        });
    }

    public function rejected(): self
    {
        return $this->state(function () {
            return [
                'status' => 'rejected',
                'rejection_reason' => 'Invalid document',
            ];
        });
    }
}


