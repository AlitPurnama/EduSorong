# Setup Email - Langkah Cepat

## ⚠️ MASALAH: Email Tidak Terkirim

Jika log menunjukkan "Email sent successfully" tapi email tidak sampai, kemungkinan besar **MAIL_MAILER masih menggunakan 'log'**.

Dengan `MAIL_MAILER=log`, email hanya ditulis ke log file (`storage/logs/laravel.log`), **TIDAK benar-benar terkirim**.

## ✅ SOLUSI: Setup Email SMTP

### Opsi 1: Gmail (Recommended untuk Production)

1. **Aktifkan 2-Step Verification** di Google Account Anda
2. **Buat App Password**:
   - Buka: https://myaccount.google.com/apppasswords
   - Pilih "Mail" dan "Other (Custom name)"
   - Masukkan nama: "EduSorong"
   - **Copy password 16 karakter** yang dihasilkan

3. **Update file `.env`**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="EduSorong"
```

**Catatan:** Masukkan App Password tanpa spasi (contoh: `abcd efgh ijkl mnop` menjadi `abcdefghijklmnop`)

### Opsi 2: Mailtrap (Recommended untuk Development/Testing)

1. **Daftar gratis** di https://mailtrap.io
2. **Buat inbox baru**
3. **Copy credentials** dari inbox
4. **Update file `.env`**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@edusorong.com
MAIL_FROM_NAME="EduSorong"
```

### Opsi 3: SMTP Lainnya

Untuk SMTP provider lain (SendGrid, Mailgun, dll), sesuaikan konfigurasi:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="EduSorong"
```

## 🔧 Setelah Update .env

1. **Clear config cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Test email**:
   ```bash
   php artisan test:email your-email@example.com
   ```

3. **Cek log** untuk melihat detail:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## 📝 Cek Status Email

Sekarang sistem akan:
- ✅ Menampilkan warning di log jika menggunakan `MAIL_MAILER=log`
- ✅ Menampilkan mailer yang digunakan di setiap log email
- ✅ Memberikan pesan warning di UI jika email hanya ditulis ke log

## 🐛 Troubleshooting

**Email masih tidak terkirim setelah setup SMTP:**
1. Cek spam folder
2. Pastikan App Password benar (untuk Gmail)
3. Cek firewall/network
4. Lihat error di log: `tail -f storage/logs/laravel.log`

**Error "Connection refused":**
- Pastikan `MAIL_HOST` dan `MAIL_PORT` benar
- Cek apakah port tidak diblokir firewall

**Error "Authentication failed":**
- Pastikan `MAIL_USERNAME` dan `MAIL_PASSWORD` benar
- Untuk Gmail, pastikan menggunakan App Password, bukan password biasa

