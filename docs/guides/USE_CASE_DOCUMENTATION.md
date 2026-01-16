# Use Case Diagram - EduSorong Platform

## Aktor (Actors)

### 1. Guest (Pengunjung)
Pengguna yang belum terdaftar atau tidak login. Dapat melihat kampanye dan melakukan donasi.

### 2. User (Pengguna Terdaftar)
Pengguna yang sudah terdaftar dan login. Dapat membuat kampanye, mengelola profil, dan melakukan berbagai aktivitas.

### 3. Admin
Administrator sistem yang bertanggung jawab untuk memverifikasi organisasi, KTP, dan mengelola request penarikan dana.

### 4. Midtrans Payment Gateway
Sistem eksternal untuk memproses pembayaran dan mengirim notifikasi status pembayaran.

---

## Use Cases

### Public Features (Fitur Publik)

#### UC1: Lihat Daftar Kampanye
- **Aktor**: Guest, User
- **Deskripsi**: Menampilkan daftar semua kampanye yang tersedia
- **Precondition**: Tidak ada
- **Postcondition**: User melihat daftar kampanye

#### UC2: Lihat Detail Kampanye
- **Aktor**: Guest, User
- **Deskripsi**: Menampilkan detail lengkap sebuah kampanye termasuk deskripsi, target, dana terkumpul, dan update
- **Precondition**: Kampanye harus ada
- **Postcondition**: User melihat detail kampanye

#### UC3: Lihat Profil User
- **Aktor**: Guest, User
- **Deskripsi**: Menampilkan profil publik dari seorang user
- **Precondition**: User harus ada
- **Postcondition**: User melihat profil

#### UC4: Donasi ke Kampanye
- **Aktor**: Guest, User
- **Deskripsi**: Melakukan donasi ke kampanye dengan berbagai metode pembayaran (QRIS, E-Wallet, Virtual Account)
- **Precondition**: Kampanye harus ada
- **Postcondition**: Payment dibuat dan menunggu pembayaran
- **Extend**: UC37 (Terima Notifikasi Pembayaran)

#### UC5: Lihat Status Pembayaran
- **Aktor**: Guest, User
- **Deskripsi**: Melihat status pembayaran yang telah dibuat
- **Precondition**: Payment harus ada
- **Postcondition**: User melihat status pembayaran

#### UC6: Lihat Feed Donasi
- **Aktor**: Guest, User
- **Deskripsi**: Melihat feed donasi terbaru (untuk running text)
- **Precondition**: Tidak ada
- **Postcondition**: User melihat feed donasi

---

### Authentication (Autentikasi)

#### UC7: Registrasi
- **Aktor**: Guest
- **Deskripsi**: Membuat akun baru di sistem
- **Precondition**: User belum terdaftar
- **Postcondition**: Akun baru dibuat, user dapat login

#### UC8: Login
- **Aktor**: Guest, User
- **Deskripsi**: Masuk ke sistem dengan email dan password
- **Precondition**: User sudah terdaftar
- **Postcondition**: User terautentikasi dan dapat mengakses fitur terproteksi

#### UC9: Logout
- **Aktor**: User
- **Deskripsi**: Keluar dari sistem
- **Precondition**: User sudah login
- **Postcondition**: Session dihapus, user logout

---

### User Management (Manajemen User)

#### UC10: Lihat Dashboard
- **Aktor**: User
- **Deskripsi**: Melihat dashboard pribadi yang menampilkan kampanye yang dibuat
- **Precondition**: User harus login
- **Postcondition**: User melihat dashboard

#### UC11: Update Profil
- **Aktor**: User
- **Deskripsi**: Mengupdate informasi profil seperti nama, foto, bio, dan nomor telepon
- **Precondition**: User harus login
- **Postcondition**: Profil terupdate

#### UC12: Ubah Password
- **Aktor**: User
- **Deskripsi**: Mengubah password akun
- **Precondition**: User harus login
- **Postcondition**: Password terupdate

#### UC13: Submit Verifikasi KTP
- **Aktor**: User
- **Deskripsi**: Mengajukan verifikasi KTP dengan mengupload foto KTP dan mengisi data
- **Precondition**: User harus login
- **Postcondition**: Request verifikasi KTP dibuat, menunggu approval admin

#### UC14: Lihat Status Verifikasi KTP
- **Aktor**: User
- **Deskripsi**: Melihat status verifikasi KTP (pending, approved, rejected)
- **Precondition**: User harus login
- **Postcondition**: User melihat status verifikasi

---

### Campaign Management (Manajemen Kampanye)

#### UC15: Buat Kampanye
- **Aktor**: User
- **Deskripsi**: Membuat kampanye baru dengan mengisi informasi seperti judul, deskripsi, target dana, dll.
- **Precondition**: User harus login
- **Postcondition**: Kampanye baru dibuat
- **Include**: UC20 (jika menggunakan organisasi terverifikasi)

#### UC16: Request Hapus Kampanye
- **Aktor**: User
- **Deskripsi**: Mengajukan request untuk menghapus kampanye
- **Precondition**: User harus login dan memiliki kampanye
- **Postcondition**: Request penghapusan dibuat, menunggu approval admin

#### UC17: Hapus Kampanye
- **Aktor**: User
- **Deskripsi**: Menghapus kampanye setelah request disetujui admin
- **Precondition**: Request penghapusan sudah disetujui admin
- **Postcondition**: Kampanye dihapus

#### UC18: Buat Update Kampanye
- **Aktor**: User
- **Deskripsi**: Membuat update/berita terbaru untuk kampanye
- **Precondition**: User harus login dan memiliki kampanye
- **Postcondition**: Update kampanye dibuat

#### UC19: Hapus Update Kampanye
- **Aktor**: User
- **Deskripsi**: Menghapus update kampanye yang telah dibuat
- **Precondition**: User harus login dan memiliki update kampanye
- **Postcondition**: Update kampanye dihapus

---

### Organization Verification (Verifikasi Organisasi)

#### UC20: Request Verifikasi Organisasi
- **Aktor**: User
- **Deskripsi**: Mengajukan verifikasi organisasi dengan mengisi data organisasi dan mengupload dokumen
- **Precondition**: User harus login
- **Postcondition**: Request verifikasi organisasi dibuat, menunggu approval admin

#### UC21: Lihat Status Verifikasi Organisasi
- **Aktor**: User
- **Deskripsi**: Melihat status verifikasi organisasi (pending, approved, rejected)
- **Precondition**: User harus login
- **Postcondition**: User melihat status verifikasi

---

### Withdrawal Management (Manajemen Penarikan Dana)

#### UC22: Request Penarikan Dana
- **Aktor**: User
- **Deskripsi**: Mengajukan request penarikan dana dari kampanye (hanya jika kampanye mencapai 80% target)
- **Precondition**: User harus login, kampanye mencapai 80% target, ada dana yang tersedia
- **Postcondition**: Request penarikan dibuat, menunggu approval admin
- **Include**: UC23 (jika diperlukan bukti)

#### UC23: Upload Bukti Penarikan
- **Aktor**: User
- **Deskripsi**: Mengupload bukti penggunaan dana yang telah ditarik
- **Precondition**: Request penarikan sudah disetujui dan memerlukan bukti
- **Postcondition**: Bukti penarikan diupload

#### UC24: Hapus Bukti Penarikan
- **Aktor**: User
- **Deskripsi**: Menghapus bukti penarikan yang telah diupload
- **Precondition**: User harus login dan memiliki bukti penarikan
- **Postcondition**: Bukti penarikan dihapus

#### UC25: Lihat Status Penarikan
- **Aktor**: User
- **Deskripsi**: Melihat status request penarikan dana (pending, approved, rejected, completed)
- **Precondition**: User harus login
- **Postcondition**: User melihat status penarikan

---

### Admin Management (Manajemen Admin)

#### UC26: Lihat Dashboard Admin
- **Aktor**: Admin
- **Deskripsi**: Melihat dashboard admin yang menampilkan semua request yang perlu ditinjau
- **Precondition**: Admin harus login
- **Postcondition**: Admin melihat dashboard

#### UC27: Approve Verifikasi Organisasi
- **Aktor**: Admin
- **Deskripsi**: Menyetujui request verifikasi organisasi
- **Precondition**: Ada request verifikasi organisasi yang pending
- **Postcondition**: Organisasi terverifikasi, dapat digunakan untuk kampanye

#### UC28: Reject Verifikasi Organisasi
- **Aktor**: Admin
- **Deskripsi**: Menolak request verifikasi organisasi dengan memberikan alasan
- **Precondition**: Ada request verifikasi organisasi yang pending
- **Postcondition**: Request ditolak, user dapat melihat alasan penolakan

#### UC29: Approve Request Penarikan
- **Aktor**: Admin
- **Deskripsi**: Menyetujui request penarikan dana
- **Precondition**: Ada request penarikan yang pending
- **Postcondition**: Request disetujui, menunggu bukti (jika diperlukan) atau dapat dilengkapi

#### UC30: Reject Request Penarikan
- **Aktor**: Admin
- **Deskripsi**: Menolak request penarikan dana dengan memberikan alasan
- **Precondition**: Ada request penarikan yang pending
- **Postcondition**: Request ditolak, user dapat melihat alasan penolakan

#### UC31: Complete Request Penarikan
- **Aktor**: Admin
- **Deskripsi**: Menandai request penarikan sebagai selesai setelah bukti diverifikasi
- **Precondition**: Request sudah disetujui dan bukti sudah diverifikasi
- **Postcondition**: Request ditandai sebagai completed

#### UC32: Approve Verifikasi KTP
- **Aktor**: Admin
- **Deskripsi**: Menyetujui verifikasi KTP user
- **Precondition**: Ada request verifikasi KTP yang pending
- **Postcondition**: KTP user terverifikasi

#### UC33: Reject Verifikasi KTP
- **Aktor**: Admin
- **Deskripsi**: Menolak verifikasi KTP user dengan memberikan alasan
- **Precondition**: Ada request verifikasi KTP yang pending
- **Postcondition**: Request ditolak, user dapat melihat alasan penolakan

#### UC34: Verifikasi Bukti Penarikan
- **Aktor**: Admin
- **Deskripsi**: Memverifikasi bukti penarikan yang diupload user
- **Precondition**: Ada bukti penarikan yang perlu diverifikasi
- **Postcondition**: Bukti diverifikasi

#### UC35: Approve Request Hapus Kampanye
- **Aktor**: Admin
- **Deskripsi**: Menyetujui request penghapusan kampanye
- **Precondition**: Ada request penghapusan kampanye yang pending
- **Postcondition**: Kampanye dapat dihapus oleh user

#### UC36: Reject Request Hapus Kampanye
- **Aktor**: Admin
- **Deskripsi**: Menolak request penghapusan kampanye dengan memberikan alasan
- **Precondition**: Ada request penghapusan kampanye yang pending
- **Postcondition**: Request ditolak, kampanye tetap ada

---

### Payment Processing (Proses Pembayaran)

#### UC37: Terima Notifikasi Pembayaran
- **Aktor**: Midtrans Payment Gateway
- **Deskripsi**: Menerima notifikasi status pembayaran dari Midtrans
- **Precondition**: Payment sudah dibuat
- **Postcondition**: Status payment terupdate sesuai notifikasi

---

## Relasi Antar Use Case

### Extend
- UC4 (Donasi) **extends** UC37 (Notifikasi Pembayaran): Notifikasi pembayaran dipanggil setelah donasi dibuat

### Include
- UC15 (Buat Kampanye) **includes** UC20 (Request Verifikasi Organisasi): Jika user memilih menggunakan organisasi terverifikasi
- UC22 (Request Penarikan) **includes** UC23 (Upload Bukti): Jika admin memerlukan bukti penarikan

### Generalization
- Admin **inherits** dari User: Admin memiliki semua kemampuan User ditambah kemampuan admin

---

## Catatan Penting

1. **Syarat Penarikan Dana**: User hanya dapat request penarikan jika kampanye mencapai minimal 80% dari target
2. **Verifikasi Organisasi**: Organisasi yang terverifikasi dapat digunakan untuk membuat kampanye
3. **Verifikasi KTP**: User dapat mengajukan verifikasi KTP untuk meningkatkan kredibilitas
4. **Metode Pembayaran**: Sistem mendukung QRIS, E-Wallet, dan Virtual Account melalui Midtrans
5. **Donasi Anonim**: Guest dan User dapat melakukan donasi secara anonim

