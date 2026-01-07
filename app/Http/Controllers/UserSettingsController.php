<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserSettingsController extends Controller
{
    public function show()
    {
        return view("settings.index", ["user" => Auth::user()]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile("photo")) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk("public")->delete($user->photo);
            }
            $data["photo"] = $request
                ->file("photo")
                ->store("profiles", "public");
        }

        $user->update($data);

        return redirect()
            ->route("settings.show")
            ->with("success", "Profil berhasil diperbarui.");
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $user->update([
            "password" => Hash::make($request->validated()["password"]),
        ]);

        return redirect()
            ->route("settings.show")
            ->with("success", "Password berhasil diubah.");
    }

    public function showKtpVerification()
    {
        $user = Auth::user();
        return view("settings.ktp-verification", compact('user'));
    }

    public function submitKtpVerification(Request $request)
    {
        $user = Auth::user();

        // Check if user already has pending or approved verification
        if (in_array($user->ktp_verification_status, ['pending', 'approved'])) {
            return redirect()
                ->route('settings.ktp.show')
                ->with('error', 'Anda sudah memiliki verifikasi KTP yang sedang diproses atau sudah disetujui. Tidak dapat mengajukan verifikasi baru.');
        }

        $validated = $request->validate([
            'ktp_number' => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'ktp_name' => ['required', 'string', 'max:255'],
            'ktp_photo' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);

        // Store KTP photo
        if ($request->hasFile('ktp_photo')) {
            // Delete old KTP photo if exists
            if ($user->ktp_photo) {
                Storage::disk('public')->delete($user->ktp_photo);
            }
            
            $ktpPhotoPath = $request->file('ktp_photo')->store('ktp', 'public');
            
            // Update user with KTP verification data and set status to pending
            $user->update([
                'ktp_number' => $validated['ktp_number'],
                'ktp_name' => $validated['ktp_name'],
                'ktp_photo' => $ktpPhotoPath,
                'ktp_verification_status' => 'pending',
                'ktp_rejection_reason' => null, // Clear any previous rejection reason
            ]);
        }

        return redirect()
            ->route('settings.ktp.show')
            ->with('success', 'Data KTP berhasil dikirim. Verifikasi sedang diproses oleh admin.');
    }

    public function profile()
    {
        $user = Auth::user();

        return view("profile.show", compact("user"));
    }
}
