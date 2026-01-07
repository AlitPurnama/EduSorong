<x-mail::message>
# Verifikasi KTP Anda Disetujui! ✅

Halo {{ $user->name }},

Kabar baik! Verifikasi KTP Anda telah **disetujui** oleh tim admin {{ config('app.name') }}.

Sekarang Anda dapat:
- Membuat kampanye penggalangan dana
- Mengajukan pencairan dana untuk kampanye Anda
- Menggunakan semua fitur yang tersedia di platform

Terima kasih telah melengkapi proses verifikasi. Kami berharap Anda dapat membantu banyak orang melalui platform kami.

<x-mail::button :url="route('dashboard')">
Buka Dashboard
</x-mail::button>

Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
Tim {{ config('app.name') }}
</x-mail::message>
