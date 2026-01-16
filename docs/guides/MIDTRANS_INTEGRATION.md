# Integrasi Midtrans Sandbox - Dokumentasi

## Arsitektur Alur Pembayaran

### 1. QRIS (Dynamic QR Code)
```
User Request → PaymentController::createQRIS()
  → MidtransService::createQRIS()
  → Midtrans API (POST /v2/charge)
  → Response: QR String & Transaction ID
  → Payment record created
  → User scan QR code & bayar
  → Midtrans notification → NotificationController::handle()
  → Payment status updated → Campaign raised_amount updated
```

### 2. E-Wallet (OVO, DANA, LINKAJA)
```
User Request → PaymentController::createEWallet()
  → MidtransService::createEWalletCharge()
  → Midtrans API (POST /v2/charge)
  → Response: Deep Link URL
  → Payment record created
  → User redirect ke deep link
  → User bayar di aplikasi E-Wallet
  → Midtrans notification → NotificationController::handle()
  → Payment status updated → Campaign raised_amount updated
```

### 3. BCA Virtual Account
```
User Request → PaymentController::createVirtualAccount()
  → MidtransService::createVirtualAccount()
  → Midtrans API (POST /v2/charge)
  → Response: Virtual Account Number
  → Payment record created
  → User transfer ke VA number
  → Midtrans notification → NotificationController::handle()
  → Payment status updated → Campaign raised_amount updated
```

## Konfigurasi

### 1. File `.env`
Tambahkan konfigurasi berikut ke file `.env`:

```env
# Midtrans Sandbox Configuration
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

**Cara mendapatkan Midtrans Server Key & Client Key (Sandbox):**
1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih **Settings** → **Access Keys**
3. Copy **Server Key** (yang dimulai dengan `SB-Mid-server-`)
4. Copy **Client Key** (yang dimulai dengan `SB-Mid-client-`)
5. Paste ke `.env`

**Catatan:** 
- Pastikan menggunakan **Sandbox** key, bukan Production
- `MIDTRANS_IS_PRODUCTION` harus `false` untuk sandbox mode
- Client Key tidak digunakan di backend, tapi berguna untuk frontend (opsional)

### 2. File `config/services.php`
Konfigurasi sudah ditambahkan otomatis:

```php
'midtrans' => [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'api_url' => env('MIDTRANS_IS_PRODUCTION', false) 
        ? 'https://api.midtrans.com' 
        : 'https://api.sandbox.midtrans.com',
],
```

## API Endpoints

### 1. Create QRIS Payment
```http
POST /payment/kampanye/{campaign}/qris
Content-Type: application/json
Authorization: Bearer {token} (jika menggunakan auth)

{
    "amount": 50000
}
```

**Response:**
```json
{
    "success": true,
    "payment": {
        "id": 1,
        "midtrans_order_id": "QRIS-1234567890-abc123",
        "qr_string": "https://...",
        "qr_url": "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=...",
        "status": "pending"
    }
}
```

### 2. Create E-Wallet Payment
```http
POST /payment/kampanye/{campaign}/ewallet
Content-Type: application/json

{
    "amount": 50000,
    "channel": "ovo",  // ovo, dana, atau linkaja
    "phone": "081234567890"  // optional untuk OVO
}
```

**Response:**
```json
{
    "success": true,
    "payment": {
        "id": 1,
        "deeplink_url": "https://...",
        "status": "pending"
    }
}
```

### 3. Create BCA Virtual Account
```http
POST /payment/kampanye/{campaign}/virtual-account
Content-Type: application/json

{
    "amount": 50000,
    "name": "John Doe"
}
```

**Response:**
```json
{
    "success": true,
    "payment": {
        "id": 1,
        "virtual_account_number": "7751234567890",
        "status": "pending"
    }
}
```

### 4. Get Payment Status
```http
GET /payment/{payment}/status
```

### 5. Notification Endpoint
```http
POST /notification/midtrans
Content-Type: application/json
```

**Notification akan menerima data:**
- `order_id` - Order ID yang digunakan
- `transaction_status` - Status transaksi (settlement, pending, deny, cancel, expire)
- `transaction_id` - Transaction ID dari Midtrans
- `gross_amount` - Jumlah pembayaran
- `signature_key` - Signature untuk verifikasi (opsional)

## Cara Testing di Midtrans Sandbox

### 1. Setup Notification URL di Midtrans Dashboard

1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih **Settings** → **Configuration**
3. Masukkan **Payment Notification URL**:
   ```
   https://your-domain.com/notification/midtrans
   ```
   Atau untuk local development, gunakan ngrok:
   ```
   https://your-ngrok-url.ngrok.io/notification/midtrans
   ```
4. Save configuration

### 2. Testing QRIS

1. **Buat payment QRIS:**
   ```bash
   curl -X POST http://localhost:8000/payment/kampanye/1/qris \
     -H "Content-Type: application/json" \
     -H "Cookie: laravel_session=..." \
     -d '{"amount": 50000}'
   ```

2. **Dapatkan QR code:**
   - Response akan berisi `qr_string` dan `qr_url`
   - Scan QR code dengan aplikasi e-wallet atau bank app
   - Atau gunakan QR code generator online dengan `qr_string`

3. **Simulasi pembayaran di Sandbox:**
   - Midtrans Sandbox tidak bisa melakukan pembayaran real
   - Gunakan **Midtrans Dashboard** → **Transactions** → **Simulate Payment**
   - Atau gunakan notification simulator

### 3. Testing E-Wallet

1. **Buat payment E-Wallet:**
   ```bash
   curl -X POST http://localhost:8000/payment/kampanye/1/ewallet \
     -H "Content-Type: application/json" \
     -d '{
       "amount": 50000,
       "channel": "ovo",
       "phone": "081234567890"
     }'
   ```

2. **Redirect ke deep link:**
   - Response berisi `deeplink_url`
   - Buka URL tersebut di browser atau aplikasi
   - Di Sandbox, akan muncul halaman simulasi pembayaran

3. **Simulasi pembayaran:**
   - Di halaman checkout, pilih status pembayaran (Success/Failed)
   - Atau gunakan notification simulator di dashboard

### 4. Testing BCA Virtual Account

1. **Buat payment VA:**
   ```bash
   curl -X POST http://localhost:8000/payment/kampanye/1/virtual-account \
     -H "Content-Type: application/json" \
     -d '{
       "amount": 50000,
       "name": "John Doe"
     }'
   ```

2. **Dapatkan nomor VA:**
   - Response berisi `virtual_account_number`
   - Di Sandbox, nomor VA adalah dummy

3. **Simulasi pembayaran:**
   - Gunakan **Midtrans Dashboard** → **Transactions** → **Simulate Payment**
   - Atau gunakan notification simulator dengan data:
     ```json
     {
       "transaction_status": "settlement",
       "order_id": "VA-1234567890-abc123",
       "transaction_id": "xxx",
       "gross_amount": "50000",
       "status_code": "200"
     }
     ```

### 5. Testing Notification (Local Development)

Untuk testing notification di local, gunakan **ngrok**:

1. **Install ngrok:**
   ```bash
   brew install ngrok  # macOS
   # atau download dari https://ngrok.com
   ```

2. **Start ngrok:**
   ```bash
   ngrok http 8000
   ```

3. **Copy HTTPS URL** (contoh: `https://abc123.ngrok.io`)

4. **Update notification URL di Midtrans Dashboard:**
   ```
   https://abc123.ngrok.io/notification/midtrans
   ```

5. **Test notification dengan curl:**
   ```bash
   curl -X POST https://abc123.ngrok.io/notification/midtrans \
     -H "Content-Type: application/json" \
     -d '{
       "transaction_status": "settlement",
       "order_id": "QRIS-1234567890-abc123",
       "transaction_id": "xxx",
       "gross_amount": "50000",
       "status_code": "200",
       "signature_key": "..."
     }'
   ```

### 6. Testing dengan Postman/Insomnia

1. **Import collection** (opsional):
   - Buat collection baru
   - Tambahkan requests untuk setiap endpoint

2. **Set environment variables:**
   - `base_url`: `http://localhost:8000`
   - `campaign_id`: ID campaign yang akan digunakan
   - `midtrans_server_key`: Server key dari Midtrans

3. **Test flow:**
   - Create payment → Get payment status → Simulate notification

## Status Payment

Status yang digunakan dalam aplikasi:
- `pending`: Menunggu pembayaran
- `paid`: Sudah dibayar (settlement)
- `expired`: Kadaluarsa
- `failed`: Gagal
- `cancel`: Dibatalkan

Status dari Midtrans (`transaction_status`):
- `pending`: Menunggu pembayaran
- `settlement`: Sudah dibayar
- `capture`: Sudah di-capture
- `authorize`: Sudah di-authorize
- `deny`: Ditolak
- `cancel`: Dibatalkan
- `expire`: Kadaluarsa
- `refund`: Di-refund

## Asumsi dan Keterbatasan

1. **Testing Environment:**
   - Semua kode hanya untuk **Sandbox/Testing**
   - Tidak ada konfigurasi production
   - Server key harus dari Midtrans Sandbox

2. **Notification:**
   - Notification harus diakses dari internet (gunakan ngrok untuk local)
   - Notification signature verification opsional (dapat diaktifkan)
   - Midtrans akan mengirim notification otomatis saat status berubah

3. **Payment Methods:**
   - QRIS: Default menggunakan GoPay acquirer, expire 24 jam
   - E-Wallet: OVO, DANA, LINKAJA (channel code lowercase)
   - Virtual Account: Hanya BCA, expire 24 jam

4. **Database:**
   - Pastikan migration sudah dijalankan: `php artisan migrate`
   - Tabel `payments` akan menyimpan semua data transaksi

5. **Campaign Integration:**
   - Setelah payment berhasil (`settlement`), `campaign.raised_amount` akan otomatis diupdate
   - Relasi: Campaign hasMany Payments

## Troubleshooting

### Error: "Invalid server key"
- Pastikan `MIDTRANS_SERVER_KEY` sudah di-set di `.env`
- Pastikan menggunakan Sandbox key (dimulai dengan `SB-Mid-server-`)
- Clear config cache: `php artisan config:clear`

### Error: "Notification not received"
- Pastikan notification URL accessible dari internet (gunakan ngrok)
- Check log: `storage/logs/laravel.log`
- Pastikan notification URL sudah di-setup di Midtrans Dashboard

### Error: "Payment not found" di notification
- Pastikan `order_id` sesuai dengan `midtrans_order_id` di database
- Check database apakah payment sudah dibuat
- Pastikan notification data sesuai dengan format yang diharapkan

### Error: "Invalid signature"
- Signature verification menggunakan formula: `sha512(order_id + status_code + gross_amount + server_key)`
- Pastikan semua parameter sesuai
- Signature verification bisa di-disable jika tidak diperlukan

## Referensi

- [Midtrans API Documentation](https://docs.midtrans.com/)
- [Midtrans Sandbox Guide](https://docs.midtrans.com/docs/core-api-overview)
- [Midtrans Notification Guide](https://docs.midtrans.com/docs/core-api-overview#notification-handling)

