# EduSorong

**Platform Crowdfunding Pendidikan Indonesia**  
*Membangun masa depan pendidikan yang lebih cerah melalui gotong royong digital.*

---

## 📋 Tentang Proyek

EduSorong adalah platform penggalangan dana (crowdfunding) berbasis web yang didedikasikan untuk keperluan pendidikan. Platform ini memfasilitasi individu, sekolah, maupun organisasi pendidikan untuk membuat kampanye penggalangan dana, menerima donasi secara transparan, dan mengelola penggunaan dana dengan akuntabilitas tinggi.

Dikembangkan dengan **Laravel 12** dan **Tailwind CSS 4**, EduSorong mengintegrasikan **Midtrans Payment Gateway** untuk kemudahan transaksi donasi melalui QRIS, E-Wallet, dan Virtual Account.

## ✨ Fitur Utama

### 🔐 Autentikasi & Manajemen Pengguna
- **Registrasi & Login Aman:** Validasi email dan password terenkripsi.
- **Verifikasi Identitas (KYC):** Unggah KTP untuk verifikasi akun pengguna.
- **Verifikasi Organisasi:** Pendaftaran organisasi dengan validasi dokumen legalitas (NPWP, dll).
- **Manajemen Profil:** Pembaruan data diri, foto profil, dan pengaturan keamanan.
- **Penghapusan Akun:** Prosedur aman dengan token verifikasi email.

### 📢 Manajemen Kampanye
- **Buat Kampanye:** Formulir detil dengan target dana, deskripsi, dan media visual.
- **Update Kampanye:** Fitur berita/kabar terbaru (updates) untuk donatur.
- **Validasi Penghapusan:** Request penghapusan kampanye memerlukan persetujuan Admin.

### 💰 Donasi & Pembayaran (Midtrans)
- **Multi-channel Payment:** Dukungan QRIS (GoPay/ShopeePay), E-Wallet (OVO, DANA, LinkAja), dan Virtual Account (BCA).
- **Donasi Anonim:** Opsi bagi donatur untuk menyembunyikan identitas publik.
- **Minimum Donasi:** Validasi nominal donasi minimum Rp 10.000.
- **Real-time Notification:** Status pembayaran otomatis terupdate via Webhook.

### 💸 Penarikan Dana (Withdrawal)
- **Syarat Penarikan:** Hanya dapat diajukan jika dana terkumpul mencapai **≥ 80%** dari target.
- **Approval Workflow:** Setiap penarikan memerlukan persetujuan Admin.
- **Bukti Penggunaan:** Kewajiban unggah bukti penggunaan dana untuk transparansi.

### 🛡️ Dashboard Admin
- **Verifikasi Terpusat:** Approval/Rejection untuk KTP, Organisasi, dan Penarikan Dana.
- **Pengawasan Kampanye:** Moderasi konten kampanye dan permintaan penghapusan.

## 🛠️ Teknologi

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade Templates, Tailwind CSS 4, Vite 7
- **Database:** SQLite (Development), MySQL/PostgreSQL (Production)
- **Payment Gateway:** Midtrans (Snap & Core API)
- **Testing:** PHPUnit 11.5

## 📋 Persyaratan Sistem

Pastikan lingkungan pengembangan Anda memenuhi syarat berikut:
- **PHP** >= 8.2
- **Composer**
- **Node.js** >= 18.x & NPM/PNPM
- **SQLite** (atau MySQL/PostgreSQL)

## 🚀 Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan proyek di lingkungan lokal:

### 1. Clone Repository
```bash
git clone https://github.com/username/EduSorong.git
cd EduSorong
```

### 2. Install Dependencies
```bash
# Backend Dependencies
composer install

# Frontend Dependencies
npm install
```

### 3. Konfigurasi Environment
Salin file konfigurasi contoh dan sesuaikan:
```bash
cp .env.example .env
```

Buka file `.env` dan atur konfigurasi berikut:
```env
# Database (Default menggunakan SQLite)
DB_CONNECTION=sqlite
# Jika menggunakan path absolut, sesuaikan DB_DATABASE

# Email (Wajib untuk fitur verifikasi)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com # Atau provider lain
MAIL_PORT=587
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_anda

# Midtrans (Mode Sandbox)
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx...
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx...
MIDTRANS_IS_PRODUCTION=false
```
> 💡 Lihat [EMAIL_SETUP.md](docs/guides/EMAIL_SETUP.md) dan [MIDTRANS_INTEGRATION.md](docs/guides/MIDTRANS_INTEGRATION.md) untuk panduan detail.

### 4. Setup Database & Key
```bash
# Generate Application Key
php artisan key:generate

# Buat file database SQLite (jika menggunakan SQLite)
touch database/database.sqlite

# Jalankan Migrasi & Seeder
php artisan migrate --seed
```

### 5. Link Storage
Penting untuk menampilkan gambar profil, kampanye, dan bukti verifikasi:
```bash
php artisan storage:link
```

## 🏃‍♂️ Menjalankan Aplikasi

Jalankan perintah berikut di terminal terpisah:

**Terminal 1 (Backend Server):**
```bash
php artisan serve
```

**Terminal 2 (Frontend Build/Watch):**
```bash
npm run dev
```

**Terminal 3 (Queue Worker - Opsional untuk Email/Jobs):**
```bash
php artisan queue:listen
```

Akses aplikasi di: `http://localhost:8000`

## 🧪 Testing

Jalankan suite pengujian untuk memastikan integritas sistem:

```bash
# Jalankan semua test
php artisan test

# Test koneksi email
php artisan test:email user@example.com
```

## 📚 Dokumentasi Terkait

- **[MIDTRANS_INTEGRATION.md](docs/guides/MIDTRANS_INTEGRATION.md)**: Detail implementasi payment gateway.
- **[EMAIL_SETUP.md](docs/guides/EMAIL_SETUP.md)**: Panduan konfigurasi SMTP Email.
- **[USE_CASE_DOCUMENTATION.md](docs/guides/USE_CASE_DOCUMENTATION.md)**: Dokumentasi alur penggunaan aplikasi.

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

---
**EduSorong Team** © 2026