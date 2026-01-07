<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Mail\AccountDeletedMail;
use App\Mail\AccountDeletionVerificationMail;
use App\Mail\EmailVerificationMail;
use App\Mail\PasswordChangedMail;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        // Send password changed email
        try {
            \Log::info('Attempting to send password changed email to: ' . $user->email);
            Mail::to($user->email)->send(new PasswordChangedMail($user));
            \Log::info('Password changed email sent successfully to: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send password changed email to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

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

    public function resendEmailVerification()
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect()
                ->route('settings.show')
                ->with('info', 'Email Anda sudah terverifikasi.');
        }

        // Generate new verification token if doesn't exist
        if (!$user->email_verification_token) {
            $user->update([
                'email_verification_token' => Str::random(64),
            ]);
            $user->refresh();
        }

        // Send email verification
        $mailer = config('mail.default');
        $verificationUrl = route('auth.verify-email', ['token' => $user->email_verification_token]);
        try {
            \Log::info('Attempting to resend email verification to: ' . $user->email . ' (mailer: ' . $mailer . ')');
            \Log::info('SMTP Config - Host: ' . config('mail.mailers.smtp.host') . ', Port: ' . config('mail.mailers.smtp.port') . ', Username: ' . config('mail.mailers.smtp.username'));
            
            $result = Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));
            
            if ($mailer === 'log') {
                \Log::warning('Email written to log only (MAIL_MAILER=log). Email not actually sent to: ' . $user->email);
                return redirect()
                    ->route('settings.show')
                    ->with('warning', 'Email ditulis ke log saja. Untuk mengirim email sebenarnya, ubah MAIL_MAILER=smtp di file .env');
            } else {
                \Log::info('Email verification resent successfully to: ' . $user->email);
                \Log::info('Mail result: ' . json_encode($result));
            }
        } catch (\Swift_TransportException $e) {
            \Log::error('SMTP Transport Error - Failed to resend email verification to ' . $user->email);
            \Log::error('Error: ' . $e->getMessage());
            \Log::error('Code: ' . $e->getCode());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()
                ->route('settings.show')
                ->with('error', 'Gagal mengirim email verifikasi: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Failed to resend email verification to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Exception class: ' . get_class($e));
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()
                ->route('settings.show')
                ->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }

        return redirect()
            ->route('settings.show')
            ->with('success', 'Email verifikasi telah dikirim. Silakan cek inbox Anda.');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);

        $user = Auth::user();
        $oldEmail = $user->email;
        $newEmail = $request->email;

        // Update email and reset verification (bypass fillable for email_verified_at)
        $verificationToken = Str::random(64);
        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => null,
            'email_verification_token' => $verificationToken,
        ])->save();

        // Send email verification to new email
        $verificationUrl = route('auth.verify-email', ['token' => $verificationToken]);
        try {
            \Log::info('Attempting to send email verification to new email: ' . $newEmail);
            Mail::to($newEmail)->send(new EmailVerificationMail($user, $verificationUrl));
            \Log::info('Email verification sent successfully to new email: ' . $newEmail);
        } catch (\Exception $e) {
            \Log::error('Failed to send email verification to new email ' . $newEmail . ': ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

        return redirect()
            ->route('settings.show')
            ->with('success', 'Email berhasil diubah. Silakan verifikasi email baru Anda.');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return redirect()
                ->route('settings.show')
                ->withErrors(['password' => 'Password tidak sesuai.']);
        }

        // Check if user has campaigns
        $campaigns = Campaign::where('user_id', $user->id)->get();
        
        if ($campaigns->count() > 0) {
            // Check if any campaign has donations
            $hasDonations = $campaigns->some(function ($campaign) {
                return $campaign->raised_amount > 0;
            });

            if ($hasDonations) {
                return redirect()
                    ->route('settings.show')
                    ->with('error', 'Tidak dapat menghapus akun karena Anda memiliki kampanye yang sudah menerima donasi. Silakan hubungi admin untuk bantuan.');
            }

            return redirect()
                ->route('settings.show')
                ->with('error', 'Tidak dapat menghapus akun karena Anda memiliki kampanye. Silakan hapus semua kampanye terlebih dahulu.');
        }

        // Generate deletion token
        $deletionToken = Str::random(64);
        $user->update([
            'account_deletion_token' => $deletionToken,
        ]);

        // Send account deletion verification email
        $verificationUrl = route('settings.account.delete-confirm', ['token' => $deletionToken]);
        try {
            \Log::info('Attempting to send account deletion verification email to: ' . $user->email);
            Mail::to($user->email)->send(new AccountDeletionVerificationMail($user->name, $user->email, $verificationUrl));
            \Log::info('Account deletion verification email sent successfully to: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send account deletion verification email to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()
                ->route('settings.show')
                ->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }

        return redirect()
            ->route('settings.show')
            ->with('success', 'Email konfirmasi penghapusan akun telah dikirim. Silakan cek email Anda untuk mengonfirmasi.');
    }

    public function confirmAccountDeletion(Request $request, string $token)
    {
        $user = User::where('account_deletion_token', $token)->first();

        if (!$user) {
            // Check if already deleted or token invalid
            if (Auth::check()) {
                Auth::logout();
            }
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('auth.show')
                ->with('error', 'Link konfirmasi tidak valid atau sudah digunakan.');
        }

        // Store user data for email before deletion
        $userName = $user->name;
        $userEmail = $user->email;

        // Double check: user should not have campaigns with donations
        $campaigns = Campaign::where('user_id', $user->id)->get();
        if ($campaigns->count() > 0) {
            $hasDonations = $campaigns->some(function ($campaign) {
                return $campaign->raised_amount > 0;
            });

            if ($hasDonations) {
                // Clear token and redirect
                $user->update(['account_deletion_token' => null]);
                
                if (Auth::check() && Auth::id() === $user->id) {
                    return redirect()->route('settings.show')
                        ->with('error', 'Tidak dapat menghapus akun karena Anda memiliki kampanye yang sudah menerima donasi.');
                }
                
                return redirect()->route('auth.show')
                    ->with('error', 'Tidak dapat menghapus akun karena memiliki kampanye yang sudah menerima donasi.');
            }
        }

        // Logout user if they're logged in
        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
        }

        // Delete user account
        $user->delete();

        // Send account deletion confirmation email
        try {
            \Log::info('Attempting to send account deletion confirmation email to: ' . $userEmail);
            Mail::to($userEmail)->send(new AccountDeletedMail($userName, $userEmail));
            \Log::info('Account deletion confirmation email sent successfully to: ' . $userEmail);
        } catch (\Exception $e) {
            \Log::error('Failed to send account deletion confirmation email to ' . $userEmail . ': ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.show')
            ->with('success', 'Akun Anda telah berhasil dihapus.');
    }
}
