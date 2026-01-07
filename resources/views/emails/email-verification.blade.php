<x-mail::message>
# Verifikasi Email Anda

Halo {{ $user->name }},

Terima kasih telah mendaftar di {{ config('app.name') }}! Untuk melengkapi pendaftaran Anda, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini.

<x-mail::button :url="$verificationUrl">
Verifikasi Email
</x-mail::button>

Atau salin dan tempel link berikut ke browser Anda:
{{ $verificationUrl }}

**Penting:** Link verifikasi ini akan kedaluwarsa dalam 24 jam. Jika Anda tidak membuat akun ini, Anda dapat mengabaikan email ini.

Jika tombol di atas tidak berfungsi, salin dan tempel URL di atas ke browser web Anda.

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
