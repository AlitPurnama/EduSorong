<x-mail::message>
# Verifikasi KTP Anda Ditolak

Halo {{ $user->name }},

Kami ingin memberitahu Anda bahwa verifikasi KTP Anda telah **ditolak** oleh tim admin {{ config('app.name') }}.

**Alasan Penolakan:**
{{ $rejectionReason }}

Jangan khawatir! Anda dapat mengajukan ulang verifikasi KTP dengan memperbaiki masalah yang disebutkan di atas.

**Langkah selanjutnya:**
1. Perbaiki data KTP sesuai dengan alasan penolakan
2. Pastikan foto KTP jelas dan dapat dibaca
3. Pastikan nomor KTP dan nama sesuai dengan dokumen asli
4. Ajukan ulang verifikasi KTP melalui halaman pengaturan

<x-mail::button :url="route('settings.ktp.show')">
Ajukan Ulang Verifikasi
</x-mail::button>

Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
