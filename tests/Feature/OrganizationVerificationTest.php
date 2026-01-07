<?php

namespace Tests\Feature;

use App\Models\OrganizationVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrganizationVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function verifiedUser(): User
    {
        return User::factory()->create([
            'ktp_verification_status' => 'approved',
            'ktp_verified' => true,
        ]);
    }

    public function test_index_shows_user_verifications(): void
    {
        $user = $this->verifiedUser();
        $this->actingAs($user);
        $this->get(route('organization.index'))
            ->assertOk()
            ->assertViewIs('organization.index');
    }

    public function test_create_requires_ktp_approved(): void
    {
        $user = User::factory()->create([
            'ktp_verification_status' => 'none',
            'ktp_verified' => false,
        ]);
        $this->actingAs($user);

        $this->get(route('organization.create'))
            ->assertRedirect(route('organization.index'))
            ->assertSessionHas('error');
    }

    public function test_store_happy_path(): void
    {
        Storage::fake('public');
        $user = $this->verifiedUser();
        $this->actingAs($user);

        $payload = [
            'organization_name' => 'Yayasan Edu Sorong',
            'organization_description' => 'Deskripsi yayasan',
            'npwp' => '123456789012345',
            'phone' => '08123456789',
            'website' => 'https://example.org',
            'address' => 'Jl. Pendidikan No. 1',
            'document' => UploadedFile::fake()->create('akta.pdf', 100, 'application/pdf'),
        ];

        $this->post(route('organization.store'), $payload)
            ->assertRedirect(route('organization.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('organization_verifications', [
            'user_id' => $user->id,
            'organization_name' => 'Yayasan Edu Sorong',
            'status' => 'pending',
        ]);

        $ov = OrganizationVerification::first();
        Storage::disk('public')->assertExists($ov->document_path);
    }
}


