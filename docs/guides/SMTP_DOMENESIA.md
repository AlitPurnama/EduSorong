# Setup SMTP Domene sia

## Konfigurasi untuk Domene sia SMTP

### 1. Update file `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.domene sia.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="EduSorong"
```

**Catatan untuk Domene sia:**
- Port biasanya: **587** (TLS) atau **465** (SSL)
- Encryption: **tls** untuk port 587, atau **ssl** untuk port 465
- Username biasanya adalah **email lengkap** (bukan hanya username)
- Pastikan email yang digunakan sudah aktif di cPanel Domene sia

### 2. Alternatif Konfigurasi (jika port 587 tidak bekerja)

**Opsi A: Port 465 dengan SSL**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.domene sia.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="EduSorong"
```

**Opsi B: Port 25 (jika tersedia)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.domene sia.com
MAIL_PORT=25
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="EduSorong"
```

### 3. Cek SMTP Settings di cPanel Domene sia

1. Login ke **cPanel Domene sia**
2. Buka **Email Accounts**
3. Pilih email yang akan digunakan
4. Klik **Connect Devices** atau **Configure Mail Client**
5. Lihat **SMTP Settings**:
   - SMTP Server: biasanya `mail.yourdomain.com` atau `smtp.domene sia.com`
   - Port: 587 (TLS) atau 465 (SSL)
   - Username: email lengkap
   - Password: password email

### 4. Test Email

Setelah konfigurasi, test dengan:

```bash
php artisan test:email your-email@example.com
```

### 5. Troubleshooting

**Email tidak terkirim tapi tidak ada error:**
1. **Cek spam folder** - Email mungkin masuk ke spam
2. **Cek log detail**: `tail -f storage/logs/laravel.log`
3. **Pastikan FROM address sama dengan username** - Beberapa SMTP server memerlukan ini
4. **Cek apakah email aktif** di cPanel

**Error "Connection refused" atau "Connection timeout":**
- Pastikan `MAIL_HOST` benar (bisa cek di cPanel)
- Cek apakah port tidak diblokir firewall
- Coba port alternatif (465 dengan SSL)

**Error "Authentication failed":**
- Pastikan `MAIL_USERNAME` adalah email lengkap (bukan hanya username)
- Pastikan `MAIL_PASSWORD` benar
- Pastikan email sudah aktif di cPanel

**Error "Relay access denied":**
- Pastikan `MAIL_FROM_ADDRESS` sama dengan `MAIL_USERNAME`
- Beberapa SMTP server tidak mengizinkan relay

**Email masuk ke spam:**
- Pastikan domain memiliki SPF record
- Pastikan domain memiliki DKIM record
- Hubungi support Domene sia untuk setup SPF/DKIM

### 6. Cek Log Detail

Sekarang sistem akan mencatat:
- SMTP Host, Port, Username yang digunakan
- Error detail jika ada masalah transport
- Mail result untuk debugging

Cek log dengan:
```bash
tail -f storage/logs/laravel.log | grep -i "email\|smtp\|mail"
```

### 7. Tips untuk Domene sia

1. **Gunakan email yang sudah dibuat di cPanel** - Jangan gunakan email yang belum dibuat
2. **FROM address harus sama dengan username** - Ini penting untuk beberapa konfigurasi
3. **Test dengan email eksternal** - Kirim ke Gmail/Yahoo untuk test
4. **Cek rate limiting** - Domene sia mungkin membatasi jumlah email per jam
5. **Hubungi support** jika masih bermasalah - Mereka bisa cek log server-side

