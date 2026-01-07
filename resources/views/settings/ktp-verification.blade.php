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

            @if (session('success'))
                <div class="mb-4 px-4 py-2 rounded-md bg-[#ECFDF3] text-[13px] text-[#166534] border border-[#BBF7D0]">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 px-4 py-2 rounded-md bg-[#FEF2F2] text-[13px] text-[#991B1B] border border-[#FECACA]">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-2 rounded-md bg-[#FEF2F2] text-[13px] text-[#991B1B] border border-[#FECACA]">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                <div class="space-y-6">
                    @php
                        $verificationStatus = $user->ktp_verification_status ?? 'none';
                        $canSubmit = in_array($verificationStatus, ['none', 'rejected']);
                    @endphp

                    {{-- Status Display --}}
                    @if($verificationStatus === 'pending')
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <p class="text-[14px] font-medium text-yellow-800">Verifikasi Sedang Diproses</p>
                                    <p class="text-[13px] text-yellow-700 mt-1">
                                        Verifikasi KTP Anda sedang dalam proses review oleh admin. Anda akan mendapat notifikasi setelah verifikasi selesai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($verificationStatus === 'approved')
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-[14px] font-medium text-green-800">Verifikasi Disetujui</p>
                                    <p class="text-[13px] text-green-700 mt-1">
                                        KTP Anda sudah terverifikasi. Terima kasih!
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($verificationStatus === 'rejected')
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-[14px] font-medium text-red-800">Verifikasi Ditolak</p>
                                    @if($user->ktp_rejection_reason)
                                        <p class="text-[13px] text-red-700 mt-1">
                                            <strong>Alasan:</strong> {{ $user->ktp_rejection_reason }}
                                        </p>
                                    @endif
                                    <p class="text-[13px] text-red-700 mt-2">
                                        Anda dapat mengajukan verifikasi ulang dengan memperbaiki data yang diperlukan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <p class="text-[14px] text-[#50545F] mb-4">
                            Untuk keamanan dan transparansi, kami memerlukan verifikasi identitas Anda melalui KTP. 
                            Data Anda akan dijaga kerahasiaannya dan hanya digunakan untuk keperluan verifikasi.
                        </p>
                    </div>

                    @if($canSubmit)
                        <form action="{{ route('settings.ktp.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            <div>
                                <label for="ktp_number" class="block text-[13px] font-medium text-[#23252F] mb-2">Nomor KTP</label>
                                <input
                                    type="text"
                                    name="ktp_number"
                                    id="ktp_number"
                                    value="{{ old('ktp_number', $user->ktp_number) }}"
                                    placeholder="Masukkan nomor KTP (16 digit)"
                                    maxlength="16"
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
                                    value="{{ old('ktp_name', $user->ktp_name) }}"
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
                                @if($user->ktp_photo)
                                    <p class="text-[12px] text-[#6B6F7A] mt-1">
                                        Foto KTP sebelumnya: <a href="{{ asset('storage/' . $user->ktp_photo) }}" target="_blank" class="text-[#3B82F6] hover:underline">Lihat</a>
                                    </p>
                                @endif
                            </div>

                            <div class="pt-4">
                                <button
                                    type="submit"
                                    class="px-6 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                                >
                                    {{ $verificationStatus === 'rejected' ? 'Ajukan Verifikasi Ulang' : 'Kirim untuk Verifikasi' }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-[13px] text-gray-700">
                                Form verifikasi tidak tersedia karena Anda sudah memiliki verifikasi yang sedang diproses atau sudah disetujui.
                            </p>
                        </div>
                    @endif

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

