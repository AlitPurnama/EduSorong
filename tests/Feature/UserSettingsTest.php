<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_is_accessible(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get(route('settings.show'))
            ->assertOk()
            ->assertViewIs('settings.index');
    }

    public function test_user_can_update_profile_with_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put(route('settings.profile.update'), [
            'name' => 'Nama Baru',
            'email' => $user->email,
            'phone' => '08123456789',
            'bio' => 'Bio singkat',
            'photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect(route('settings.show'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Nama Baru', $user->name);
        $this->assertNotNull($user->photo);
        Storage::disk('public')->assertExists($user->photo);
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('oldpass')]);
        $this->actingAs($user);

        $response = $this->put(route('settings.password.update'), [
            'current_password' => 'oldpass',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('settings.show'));
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_user_can_submit_ktp_verification(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'ktp_verification_status' => 'none',
            'ktp_photo' => null,
        ]);

        $this->actingAs($user);
        $response = $this->post(route('settings.ktp.submit'), [
            'ktp_number' => '1234567890123456',
            'ktp_name' => 'Nama KTP',
            'ktp_photo' => UploadedFile::fake()->image('ktp.jpg'),
        ]);

        $response->assertRedirect(route('settings.ktp.show'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('pending', $user->ktp_verification_status);
        $this->assertNotNull($user->ktp_photo);
        Storage::disk('public')->assertExists($user->ktp_photo);
    }
}


