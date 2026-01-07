<x-mail::message>
# Selamat Datang di {{ config('app.name') }}!

Halo {{ $user->name }},

Terima kasih telah bergabung dengan {{ config('app.name') }}! Akun Anda telah berhasil dibuat.

Email Anda: **{{ $user->email }}**

Anda sekarang dapat:
- Membuat kampanye penggalangan dana
- Berdonasi untuk kampanye yang Anda pedulikan
- Melacak donasi dan kampanye Anda

Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.

<x-mail::button :url="route('dashboard')">
Masuk ke Dashboard
</x-mail::button>

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
