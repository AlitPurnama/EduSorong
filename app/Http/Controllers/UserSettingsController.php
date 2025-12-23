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
        return view('settings.index', ['user' => Auth::user()]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profiles', 'public');
        }

        $user->update($data);

        return redirect()->route('settings.show')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()->route('settings.show')->with('success', 'Password berhasil diubah.');
    }

    public function showKtpVerification()
    {
        return view('settings.ktp-verification');
    }
}

