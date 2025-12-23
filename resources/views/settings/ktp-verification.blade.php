<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Verifikasi KTP - EduSorong</title>

        <!-- Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FFF7D6] text-[#23252F] font-sans text-[14px]">
        <x-navbar />

        <main class="max-w-4xl mx-auto px-4 lg:px-0 pt-10 pb-16">
            <a href="{{ route('settings.show') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-6 inline-block">
                &larr; Kembali ke Pengaturan
            </a>

            <h1 class="text-[28px] font-semibold mb-6">Verifikasi KTP</h1>

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                <div class="space-y-6">
                    <div>
                        <p class="text-[14px] text-[#50545F] mb-4">
                            Untuk keamanan dan transparansi, kami memerlukan verifikasi identitas Anda melalui KTP. 
                            Data Anda akan dijaga kerahasiaannya dan hanya digunakan untuk keperluan verifikasi.
                        </p>
                    </div>

                    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <div>
                            <label for="ktp_number" class="block text-[13px] font-medium text-[#23252F] mb-2">Nomor KTP</label>
                            <input
                                type="text"
                                name="ktp_number"
                                id="ktp_number"
                                placeholder="Masukkan nomor KTP"
                                class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                required
                            >
                        </div>

                        <div>
                            <label for="ktp_name" class="block text-[13px] font-medium text-[#23252F] mb-2">Nama Sesuai KTP</label>
                            <input
                                type="text"
                                name="ktp_name"
                                id="ktp_name"
                                placeholder="Masukkan nama sesuai KTP"
                                class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                required
                            >
                        </div>

                        <div>
                            <label for="ktp_photo" class="block text-[13px] font-medium text-[#23252F] mb-2">Foto KTP</label>
                            <input
                                type="file"
                                name="ktp_photo"
                                id="ktp_photo"
                                accept="image/*"
                                class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                required
                            >
                            <p class="text-[12px] text-[#6B6F7A] mt-1">Format: JPG, PNG. Maksimal 2MB. Pastikan foto jelas dan terbaca.</p>
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                class="px-6 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                            >
                                Kirim untuk Verifikasi
                            </button>
                        </div>
                    </form>

                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <p class="text-[12px] text-[#6B6F7A]">
                            <strong>Catatan:</strong> Proses verifikasi biasanya memakan waktu 1-3 hari kerja. 
                            Anda akan mendapat notifikasi setelah verifikasi selesai.
                        </p>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>

