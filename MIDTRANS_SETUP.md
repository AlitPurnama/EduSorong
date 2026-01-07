# Quick Setup Guide - Midtrans Integration

## 1. Install Dependencies

Tidak ada dependency tambahan yang diperlukan. Aplikasi menggunakan HTTP client Laravel yang sudah built-in.

## 2. Setup Database

Jalankan migration untuk membuat tabel `payments`:

```bash
php artisan migrate
```

## 3. Konfigurasi Environment

Tambahkan ke file `.env`:

```env
# Midtrans Sandbox Configuration
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

**Cara mendapatkan Midtrans Server Key:**

1. Daftar/Login di [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih **Settings** → **Access Keys**
3. Copy **Server Key** (dimulai dengan `SB-Mid-server-`)
4. Copy **Client Key** (dimulai dengan `SB-Mid-client-`)
5. Paste ke `.env`

## 4. Clear Config Cache

```bash
php artisan config:clear
```

## 5. Setup Notification URL (untuk local development)

### Menggunakan ngrok:

1. Install ngrok:

    ```bash
    brew install ngrok  # macOS
    # atau download dari https://ngrok.com
    ```

2. Start ngrok:

    ```bash
    ngrok http 8000
    ```

3. Copy HTTPS URL (contoh: `https://abc123.ngrok.io`)

4. Setup notification URL di Midtrans Dashboard:
    - Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
    - Pilih **Settings** → **Configuration**
    - Masukkan **Payment Notification URL**: `https://abc123.ngrok.io/notification/midtrans`
    - Save

## 6. Testing

### Test QRIS:

```bash
curl -X POST http://localhost:8000/payment/kampanye/1/qris \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{"amount": 50000}'
```

### Test E-Wallet:

```bash
curl -X POST http://localhost:8000/payment/kampanye/1/ewallet \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{
    "amount": 50000,
    "channel": "ovo"
  }'
```

### Test Virtual Account:

```bash
curl -X POST http://localhost:8000/payment/kampanye/1/virtual-account \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{
    "amount": 50000,
    "name": "John Doe"
  }'
```

## File yang Dibuat

1. **Migration**: `database/migrations/2025_01_20_000000_create_payments_table.php`
2. **Model**: `app/Models/Payment.php`
3. **Service**: `app/Services/MidtransService.php`
4. **Controllers**:
    - `app/Http/Controllers/PaymentController.php`
    - `app/Http/Controllers/NotificationController.php`
5. **Views**:
    - `resources/views/payment/success.blade.php`
    - `resources/views/payment/failed.blade.php`
6. **Config**: Updated `config/services.php`
7. **Routes**: Updated `routes/web.php`
8. **Middleware**: Updated `bootstrap/app.php` (CSRF exclusion)
9. **Model**: Updated `app/Models/Campaign.php` (relasi payments)

## Dokumentasi Lengkap

Lihat file `MIDTRANS_INTEGRATION.md` untuk dokumentasi lengkap termasuk:

-   Arsitektur alur pembayaran
-   API endpoints detail
-   Cara testing di sandbox
-   Troubleshooting
