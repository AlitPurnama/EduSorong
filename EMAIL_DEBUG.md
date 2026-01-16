# Email Debug Guide

## Masalah: Email Tidak Terkirim

Jika email tidak terkirim, ikuti langkah-langkah berikut:

### 1. Cek Konfigurasi Email di `.env`

Pastikan file `.env` memiliki konfigurasi email yang benar:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@edusorong.com
MAIL_FROM_NAME="EduSorong"
```

**Catatan:** 
- Untuk Gmail, gunakan **App Password**, bukan password biasa
- Untuk development, bisa gunakan `MAIL_MAILER=log` untuk melihat email di log file

### 2. Cek Log File

Email yang dikirim akan tercatat di log file. Cek dengan:

```bash
tail -f storage/logs/laravel.log
```

Atau cari log email dengan:

```bash
grep -i "email" storage/logs/laravel.log
```

### 3. Test Email dengan Mailtrap (Development)

Untuk development, gunakan Mailtrap:

1. Daftar di https://mailtrap.io
2. Buat inbox baru
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

### 4. Test Email dengan Gmail SMTP

1. Aktifkan 2-Step Verification di Google Account
2. Buat App Password:
   - Buka https://myaccount.google.com/apppasswords
   - Pilih "Mail" dan "Other (Custom name)"
   - Masukkan nama (misalnya: "EduSorong")
   - Copy password yang dihasilkan
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="EduSorong"
```

### 5. Clear Config Cache

Setelah mengubah `.env`, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

### 6. Test Email dengan Tinker

Test email langsung dengan Laravel Tinker:

```bash
php artisan tinker
```

Kemudian jalankan:

```php
use App\Mail\EmailVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

$user = User::first();
$token = 'test-token';
$url = route('auth.verify-email', ['token' => $token]);

Mail::to($user->email)->send(new EmailVerificationMail($user, $url));
```

### 7. Cek Logging

Sekarang semua email memiliki logging detail. Cek log untuk melihat:
- Apakah email dipanggil: `Attempting to send...`
- Apakah email berhasil: `sent successfully`
- Jika error: `Failed to send...` dengan detail error

### 8. Troubleshooting

**Email tidak terkirim tapi tidak ada error:**
- Cek apakah `MAIL_MAILER=log` (email hanya ditulis ke log, tidak terkirim)
- Cek spam folder
- Pastikan konfigurasi SMTP benar

**Error "Connection refused":**
- Pastikan `MAIL_HOST` dan `MAIL_PORT` benar
- Cek firewall/network

**Error "Authentication failed":**
- Pastikan `MAIL_USERNAME` dan `MAIL_PASSWORD` benar
- Untuk Gmail, pastikan menggunakan App Password

**Error "Could not instantiate mailer":**
- Pastikan semua konfigurasi email di `.env` sudah diisi
- Jalankan `php artisan config:clear`

