<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CampaignManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function makeVerifiedUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'ktp_verification_status' => 'approved',
            'ktp_verified' => true,
        ], $overrides));
    }

    public function test_user_must_be_ktp_verified_to_access_create_campaign(): void
    {
        $user = User::factory()->create([
            'ktp_verification_status' => 'none',
            'ktp_verified' => false,
        ]);

        $this->actingAs($user);
        $this->get(route('dashboard.campaigns.create'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_verified_user_can_view_create_campaign_page(): void
    {
        $user = $this->makeVerifiedUser();
        $this->actingAs($user);
        $this->get(route('dashboard.campaigns.create'))
            ->assertOk()
            ->assertViewIs('dashboard.campaigns.create');
    }

    public function test_verified_user_can_store_campaign(): void
    {
        Storage::fake('public');
        $user = $this->makeVerifiedUser();
        $this->actingAs($user);

        $payload = [
            'title' => 'Bantu Pendidikan Papua',
            'location' => 'Sorong',
            'target_amount' => 1000000,
            'excerpt' => 'Ringkasan',
            'description' => 'Deskripsi kampanye',
            'image' => UploadedFile::fake()->image('cover.jpg'),
        ];

        $this->post(route('dashboard.campaigns.store'), $payload)
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('campaigns', [
            'title' => 'Bantu Pendidikan Papua',
            'user_id' => $user->id,
        ]);
    }

    public function test_request_deletion_immediately_deletes_campaign_when_no_donations(): void
    {
        $user = $this->makeVerifiedUser();
        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'raised_amount' => 0,
        ]);

        $this->actingAs($user);
        $this->get(route('dashboard.campaigns.request-deletion', $campaign))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
    }

    public function test_request_deletion_shows_form_when_has_donations(): void
    {
        $user = $this->makeVerifiedUser();
        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'raised_amount' => 10000,
        ]);

        $this->actingAs($user);
        $this->get(route('dashboard.campaigns.request-deletion', $campaign))
            ->assertOk()
            ->assertViewIs('campaigns.request-deletion');
    }
}


