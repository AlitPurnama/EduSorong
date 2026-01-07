<x-mail::message>
# Akun Anda Telah Dihapus

Halo {{ $userName }},

Kami ingin memberitahu Anda bahwa akun Anda dengan email **{{ $userEmail }}** telah berhasil dihapus dari {{ config('app.name') }}.

Semua data yang terkait dengan akun Anda telah dihapus secara permanen sesuai dengan permintaan Anda.

Jika Anda tidak meminta penghapusan akun ini, harap segera hubungi tim dukungan kami.

Terima kasih telah menggunakan {{ config('app.name') }}.

Salam,<br>
Tim {{ config('app.name') }}
</x-mail::message>
