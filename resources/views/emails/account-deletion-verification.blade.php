<x-mail::message>
# Konfirmasi Penghapusan Akun

Halo {{ $userName }},

Kami menerima permintaan untuk menghapus akun Anda di {{ config('app.name') }}.

**Email:** {{ $userEmail }}

Untuk mengonfirmasi penghapusan akun, silakan klik tombol di bawah ini. Setelah dikonfirmasi, akun Anda akan dihapus secara permanen dan tidak dapat dikembalikan.

<x-mail::button :url="$verificationUrl">
Konfirmasi Hapus Akun
</x-mail::button>

Atau salin dan tempel link berikut ke browser Anda:
{{ $verificationUrl }}

**Penting:** 
- Link ini akan kedaluwarsa dalam 24 jam
- Setelah akun dihapus, semua data akan hilang secara permanen
- Jika Anda tidak meminta penghapusan ini, abaikan email ini dan akun Anda tetap aman

Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
