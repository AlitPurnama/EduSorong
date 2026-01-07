<x-mail::message>
# Password Berhasil Diubah

Halo {{ $user->name }},

Ini adalah konfirmasi bahwa password akun Anda di {{ config('app.name') }} telah berhasil diubah.

**Detail Perubahan:**
- Email: {{ $user->email }}
- Waktu: {{ now()->format('d F Y, H:i') }} WIB

Jika Anda tidak melakukan perubahan password ini, **segera hubungi tim dukungan kami** karena akun Anda mungkin telah diretas.

Untuk keamanan akun Anda, kami sarankan untuk:
- Menggunakan password yang kuat dan unik
- Tidak membagikan password Anda kepada siapa pun
- Mengaktifkan autentikasi dua faktor jika tersedia

<x-mail::button :url="route('settings.show')">
Buka Pengaturan
</x-mail::button>

Jika Anda memiliki pertanyaan atau kekhawatiran, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
