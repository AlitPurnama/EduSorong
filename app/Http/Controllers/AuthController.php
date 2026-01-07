<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\AccountCreatedMail;
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function show(Request $request)
    {
        $mode = $request->query('mode', 'login');
        if (! in_array($mode, ['login', 'register'])) {
            $mode = 'login';
        }

        return view('auth.index', ['mode' => $mode]);
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->credentials(), $request->remember())) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->onlyInput('email');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // Generate email verification token
        $verificationToken = Str::random(64);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verification_token' => $verificationToken,
        ]);

        // Send account created email
        $mailer = config('mail.default');
        try {
            \Log::info('Attempting to send account created email to: ' . $user->email . ' (mailer: ' . $mailer . ')');
            \Log::info('SMTP Config - Host: ' . config('mail.mailers.smtp.host') . ', Port: ' . config('mail.mailers.smtp.port') . ', Username: ' . config('mail.mailers.smtp.username'));
            
            $result = Mail::to($user->email)->send(new AccountCreatedMail($user));
            
            if ($mailer === 'log') {
                \Log::warning('Email written to log only (MAIL_MAILER=log). Email not actually sent to: ' . $user->email);
            } else {
                \Log::info('Account created email sent successfully to: ' . $user->email);
                \Log::info('Mail result: ' . json_encode($result));
            }
        } catch (\Swift_TransportException $e) {
            \Log::error('SMTP Transport Error - Failed to send account created email to ' . $user->email);
            \Log::error('Error: ' . $e->getMessage());
            \Log::error('Code: ' . $e->getCode());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        } catch (\Exception $e) {
            \Log::error('Failed to send account created email to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Exception class: ' . get_class($e));
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

        // Send email verification
        $verificationUrl = route('auth.verify-email', ['token' => $verificationToken]);
        try {
            \Log::info('Attempting to send email verification to: ' . $user->email . ' (mailer: ' . $mailer . ')');
            \Log::info('SMTP Config - Host: ' . config('mail.mailers.smtp.host') . ', Port: ' . config('mail.mailers.smtp.port') . ', Username: ' . config('mail.mailers.smtp.username'));
            
            $result = Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));
            
            if ($mailer === 'log') {
                \Log::warning('Email written to log only (MAIL_MAILER=log). Email not actually sent to: ' . $user->email);
            } else {
                \Log::info('Email verification sent successfully to: ' . $user->email);
                \Log::info('Mail result: ' . json_encode($result));
            }
        } catch (\Swift_TransportException $e) {
            \Log::error('SMTP Transport Error - Failed to send email verification to ' . $user->email);
            \Log::error('Error: ' . $e->getMessage());
            \Log::error('Code: ' . $e->getCode());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        } catch (\Exception $e) {
            \Log::error('Failed to send email verification to ' . $user->email . ': ' . $e->getMessage());
            \Log::error('Exception class: ' . get_class($e));
            \Log::error('Exception trace: ' . $e->getTraceAsString());
        }

        Auth::login($user);

        return redirect()->intended('/')
            ->with('success', 'Akun berhasil dibuat! Silakan cek email Anda untuk verifikasi email.');
    }

    public function verifyEmail(Request $request, string $token)
    {
        $user = User::where('email_verification_token', $token)
            ->whereNull('email_verified_at')
            ->first();

        if (!$user) {
            // Check if user is already verified
            $alreadyVerified = User::where('email_verification_token', $token)
                ->whereNotNull('email_verified_at')
                ->first();
            
            if ($alreadyVerified) {
                // User already verified, check if logged in
                if (Auth::check() && Auth::id() === $alreadyVerified->id) {
                    return redirect()->route('settings.show')
                        ->with('info', 'Email Anda sudah terverifikasi sebelumnya.');
                }
                return redirect()->route('auth.show')
                    ->with('info', 'Email sudah terverifikasi. Silakan login.');
            }
            
            return redirect()->route('auth.show')
                ->with('error', 'Link verifikasi tidak valid atau sudah digunakan.');
        }

        // Update email verification (bypass fillable using forceFill)
        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ])->save();

        // Refresh user model to get updated data
        $user->refresh();

        // Check if user is currently logged in
        if (Auth::check() && Auth::id() === $user->id) {
            // User is logged in, redirect to settings with success message
            return redirect()->route('settings.show')
                ->with('success', 'Email berhasil diverifikasi!');
        }

        // User is not logged in, redirect to login page
        return redirect()->route('auth.show')
            ->with('success', 'Email berhasil diverifikasi! Silakan login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}


