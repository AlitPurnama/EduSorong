<?php

namespace App\Console\Commands;

use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mailer = config('mail.default');
        $this->info("Current mailer: {$mailer}");
        
        if ($mailer === 'log') {
            $this->warn("⚠️  WARNING: Mailer is set to 'log' - emails will only be written to log file, not actually sent!");
            $this->warn("   To send real emails, set MAIL_MAILER=smtp in your .env file");
            $this->newLine();
        }

        $email = $this->argument('email') ?? $this->ask('Enter email address to test');
        
        if (!$email) {
            $this->error('Email address is required');
            return 1;
        }

        // Check if user exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->warn("User with email {$email} not found. Creating test user...");
            $user = User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => bcrypt('password'),
            ]);
        }

        $this->info("Sending test email to: {$email}");
        $this->info("SMTP Host: " . config('mail.mailers.smtp.host'));
        $this->info("SMTP Port: " . config('mail.mailers.smtp.port'));
        $this->info("SMTP Username: " . config('mail.mailers.smtp.username'));
        $this->info("From Address: " . config('mail.from.address'));
        $this->newLine();
        
        try {
            $token = 'test-token-' . time();
            $verificationUrl = route('auth.verify-email', ['token' => $token]);
            
            $this->info("Attempting to send email...");
            $result = Mail::to($email)->send(new EmailVerificationMail($user, $verificationUrl));
            
            $this->info("✅ Email sent successfully!");
            $this->info("Mail result: " . json_encode($result));
            $this->newLine();
            
            if ($mailer === 'log') {
                $this->warn("📝 Email written to log only (MAIL_MAILER=log)");
                $this->info("📝 Check storage/logs/laravel.log for the email content");
            } else {
                $this->info("📧 Check your inbox (and spam folder) for the email");
                $this->warn("⚠️  If email not received, check:");
                $this->warn("   1. Spam/Junk folder");
                $this->warn("   2. SMTP credentials are correct");
                $this->warn("   3. SMTP server allows sending from your domain");
                $this->warn("   4. Check storage/logs/laravel.log for detailed errors");
            }
            
            return 0;
        } catch (\Swift_TransportException $e) {
            $this->error("❌ SMTP Transport Error!");
            $this->error("Error: " . $e->getMessage());
            $this->error("Code: " . $e->getCode());
            $this->newLine();
            $this->warn("Common issues:");
            $this->warn("  - Wrong SMTP credentials");
            $this->warn("  - SMTP server not accessible");
            $this->warn("  - Port blocked by firewall");
            $this->warn("  - SSL/TLS certificate issues");
            return 1;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            $this->error("Exception class: " . get_class($e));
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
