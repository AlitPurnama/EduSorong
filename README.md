# EduSorong

Platform crowdfunding untuk pendidikan yang memungkinkan pengguna membuat kampanye penggalangan dana, melakukan donasi, dan mengelola penarikan dana dengan integrasi payment gateway Midtrans.

## 📋 Tentang EduSorong

EduSorong adalah platform crowdfunding yang fokus pada penggalangan dana untuk keperluan pendidikan. Platform ini memungkinkan pengguna untuk:

- Membuat dan mengelola kampanye penggalangan dana
- Melakukan donasi dengan berbagai metode pembayaran (QRIS, E-Wallet, Virtual Account)
- Mengelola penarikan dana dengan sistem verifikasi
- Verifikasi identitas (KTP) dan organisasi untuk meningkatkan kredibilitas
- Dashboard admin untuk mengelola verifikasi dan approval

## ✨ Fitur Utama

### 🔐 Autentikasi
- Registrasi pengguna baru
- Login/Logout
- Validasi email unik
- Validasi password

### 📢 Manajemen Kampanye
- Buat kampanye baru dengan data lengkap (judul, deskripsi, target dana, lokasi, gambar)
- Lihat daftar kampanye publik
- Lihat detail kampanye (progress donasi, update, form donasi)
- Buat dan hapus update kampanye
- Request penghapusan kampanye (dengan approval admin)

### 💰 Donasi dan Pembayaran
- Donasi dengan berbagai metode pembayaran:
  - **QRIS** (Quick Response Code Indonesian Standard)
  - **E-Wallet** (OVO, DANA, LINKAJA)
  - **Virtual Account** (BCA)
- Validasi minimum donasi (Rp 10.000)
- Donasi anonim (guest dan user terdaftar)
- Tracking status pembayaran
- Integrasi webhook Midtrans untuk notifikasi pembayaran
- Feed donasi real-time (running text)

### 👤 Manajemen Profil dan Pengaturan
- Update profil (nama, email, bio, nomor telepon, foto)
- Ubah password dengan validasi password saat ini
- Verifikasi KTP (upload foto KTP dan data)
- Lihat status verifikasi KTP

### 🏢 Verifikasi Organisasi
- Request verifikasi organisasi dengan dokumen
- Lihat status verifikasi organisasi
- Organisasi terverifikasi dapat digunakan untuk kampanye

### 💸 Manajemen Penarikan Dana
- Request penarikan dana (hanya jika kampanye mencapai ≥ 80% target)
- Upload bukti penggunaan dana
- Lihat status penarikan (pending, approved, rejected, completed)
- Workflow approval oleh admin

### 👨‍💼 Fitur Admin
- Dashboard admin dengan overview semua request
- Approve/Reject verifikasi organisasi
- Approve/Reject verifikasi KTP
- Approve/Reject request penarikan dana
- Complete request penarikan setelah verifikasi bukti
- Approve/Reject request penghapusan kampanye
- Verifikasi bukti penarikan dana

## 🛠️ Tech Stack

### Backend
- **PHP 8.2+**
- **Laravel 12**
- **SQLite** (default, dapat diganti dengan MySQL/PostgreSQL)

### Frontend
- **Tailwind CSS 4**
- **Vite** (build tool)
- **Blade** (templating engine)
- **Lucide Icons** (icon library)

### Payment Gateway
- **Midtrans** (QRIS, E-Wallet, Virtual Account)

### Development Tools
- **Laravel Pint** (code style)
- **PHPUnit** (testing)
- **Laravel Pail** (log viewer)

## 📋 Requirements

Sebelum memulai, pastikan sistem Anda memiliki:

- **PHP** >= 8.2
- **Composer** (PHP package manager)
- **Node.js** >= 18.x dan **npm** atau **pnpm**
- **SQLite** (atau MySQL/PostgreSQL untuk production)

## 🚀 Instalasi dan Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd EduSorong
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
# atau jika menggunakan pnpm
pnpm install
```

### 3. Setup Environment

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

Atau untuk MySQL/PostgreSQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edusorong
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Setup Database

```bash
# Buat database SQLite (jika menggunakan SQLite)
touch database/database.sqlite

# Jalankan migration
php artisan migrate

# (Opsional) Jalankan seeder jika ada
php artisan db:seed
```

### 6. Konfigurasi Midtrans

Edit file `.env` dan tambahkan konfigurasi Midtrans:

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
- Pastikan menggunakan **Sandbox** key untuk development
- `MIDTRANS_IS_PRODUCTION` harus `false` untuk sandbox mode
- Untuk production, ubah ke `true` dan gunakan production keys

### 7. Setup Storage Link

```bash
# Buat symbolic link untuk storage
php artisan storage:link
```

### 8. Build Assets

```bash
# Build assets untuk production
npm run build

# Atau jalankan dalam mode development (watch mode)
npm run dev
```

### 9. Jalankan Server

```bash
# Jalankan Laravel development server
php artisan serve

# Aplikasi akan berjalan di http://localhost:8000
```

### 10. Setup Notification URL (untuk Midtrans)

Untuk testing notification di local development, gunakan **ngrok**:

```bash
# Install ngrok (macOS)
brew install ngrok

# Start ngrok
ngrok http 8000
```

Copy HTTPS URL dari ngrok (contoh: `https://abc123.ngrok.io`), lalu:

1. Login ke [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Pilih **Settings** → **Configuration**
3. Masukkan **Payment Notification URL**:
   ```
   https://your-ngrok-url.ngrok.io/notification/midtrans
   ```
4. Save configuration

## 📁 Struktur Project

```
EduSorong/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Controller untuk handling request
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form request validation
│   ├── Models/                 # Eloquent models
│   ├── Policies/               # Authorization policies
│   ├── Providers/              # Service providers
│   ├── Services/               # Business logic services
│   └── View/
│       └── Components/         # Blade components
├── config/                     # Konfigurasi aplikasi
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── public/                     # Public assets
├── resources/
│   ├── css/                    # CSS files
│   ├── js/                     # JavaScript files
│   └── views/                  # Blade templates
├── routes/
│   └── web.php                 # Web routes
├── storage/                    # Storage untuk file uploads
└── tests/                      # Test files
```

## 🔧 Konfigurasi Tambahan

### Menjadi Admin

Untuk membuat user menjadi admin, edit database langsung atau buat seeder:

```php
// Di database seeder atau tinker
$user = User::where('email', 'admin@example.com')->first();
$user->role = 'admin';
$user->save();
```

### Konfigurasi File Upload

Pastikan folder storage memiliki permission yang tepat:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Jalankan test dengan coverage
php artisan test --coverage
```

## 📝 Scripts yang Tersedia

### Composer Scripts

```bash
# Setup lengkap (install dependencies, generate key, migrate, build assets)
composer setup

# Development mode (server + queue + logs + vite)
composer dev

# Run tests
composer test
```

### NPM Scripts

```bash
# Build assets untuk production
npm run build

# Development mode dengan hot reload
npm run dev
```

## 🔐 Security

- Password di-hash menggunakan bcrypt
- CSRF protection untuk semua form
- Validasi input pada semua endpoint
- File upload validation
- Signature verification untuk Midtrans notification (opsional)

## 📚 Dokumentasi Tambahan

- [MIDTRANS_INTEGRATION.md](./MIDTRANS_INTEGRATION.md) - Dokumentasi integrasi Midtrans
- [USE_CASE_DOCUMENTATION.md](./USE_CASE_DOCUMENTATION.md) - Dokumentasi use case
- [blackbox_testing.tex](./blackbox_testing.tex) - Dokumentasi hasil testing

## 🐛 Troubleshooting

### Error: "Invalid server key"
- Pastikan `MIDTRANS_SERVER_KEY` sudah di-set di `.env`
- Pastikan menggunakan Sandbox key (dimulai dengan `SB-Mid-server-`)
- Clear config cache: `php artisan config:clear`

### Error: "Notification not received"
- Pastikan notification URL accessible dari internet (gunakan ngrok untuk local)
- Check log: `storage/logs/laravel.log`
- Pastikan notification URL sudah di-setup di Midtrans Dashboard

### Error: "Storage link not found"
```bash
php artisan storage:link
```

### Error: "Permission denied" pada storage
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🤝 Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Project ini menggunakan [MIT License](https://opensource.org/licenses/MIT).

## 👥 Authors

- **Tim Development EduSorong**

## 🙏 Acknowledgments

- Laravel Framework
- Midtrans Payment Gateway
- Tailwind CSS
- Semua kontributor dan pengguna platform

---

**EduSorong** - Bergabunglah dalam Gerakan Kebaikan untuk Masa Depan Pendidikan yang Lebih Cerah 🎓
