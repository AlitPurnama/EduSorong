<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Verifikasi Organisasi - EduSorong</title>

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

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-[28px] font-semibold">Verifikasi Organisasi/Yayasan</h1>
                @php
                    $pendingCount = $verifications->where('status', 'pending')->count();
                    $approvedCount = $verifications->where('status', 'approved')->count();
                    $canCreate = $pendingCount === 0 && $approvedCount < 3;
                @endphp
                @if($canCreate && $ktpVerified)
                    <a
                        href="{{ route('organization.create') }}"
                        class="px-5 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                    >
                        + Verifikasi Organisasi Baru
                    </a>
                @elseif(!$ktpVerified)
                    <a
                        href="{{ route('settings.ktp.show') }}"
                        class="px-5 py-2.5 rounded-full bg-yellow-500 text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-yellow-600 transition-colors"
                    >
                        Verifikasi KTP Terlebih Dahulu
                    </a>
                @endif
            </div>

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

            @if(!$ktpVerified)
                <div class="mb-6 px-4 py-3 rounded-md bg-yellow-50 text-[13px] text-yellow-800 border border-yellow-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-medium mb-1">Verifikasi KTP Diperlukan</p>
                            <p class="text-[12px] text-yellow-700">
                                Anda harus verifikasi KTP terlebih dahulu sebelum dapat mengajukan verifikasi organisasi/yayasan. 
                                <a href="{{ route('settings.ktp.show') }}" class="underline font-medium">Klik di sini untuk verifikasi KTP</a>.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($verifications->isEmpty())
                <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6 text-center">
                    <p class="text-[13px] text-[#6B6F7A] mb-4">
                        Anda belum memiliki verifikasi organisasi. Mulai verifikasi organisasi pertama Anda.
                    </p>
                    @if($ktpVerified)
                        <a
                            href="{{ route('organization.create') }}"
                            class="inline-block px-6 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                        >
                            + Verifikasi Organisasi Baru
                        </a>
                    @else
                        <a
                            href="{{ route('settings.ktp.show') }}"
                            class="inline-block px-6 py-2.5 rounded-full bg-yellow-500 text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-yellow-600 transition-colors"
                        >
                            Verifikasi KTP Terlebih Dahulu
                        </a>
                    @endif
                </section>
            @else
                <div class="space-y-4">
                    @foreach($verifications as $verification)
                        <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-[16px] font-semibold text-[#23252F]">{{ $verification->organization_name }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                            @if($verification->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($verification->status === 'approved') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($verification->status) }}
                                        </span>
                                    </div>
                                    
                                    @if($verification->organization_description)
                                        <p class="text-[13px] text-[#50545F] mb-3">{{ $verification->organization_description }}</p>
                                    @endif

                                    <div class="grid md:grid-cols-2 gap-3 text-[12px] mb-3">
                                        @if($verification->npwp)
                                            <div>
                                                <p class="text-[#6B6F7A]">NPWP:</p>
                                                <p class="font-medium text-[#23252F]">{{ $verification->npwp }}</p>
                                            </div>
                                        @endif
                                        @if($verification->phone)
                                            <div>
                                                <p class="text-[#6B6F7A]">Telepon:</p>
                                                <p class="font-medium text-[#23252F]">{{ $verification->phone }}</p>
                                            </div>
                                        @endif
                                        @if($verification->website)
                                            <div>
                                                <p class="text-[#6B6F7A]">Website:</p>
                                                <a href="{{ $verification->website }}" target="_blank" class="font-medium text-[#3B82F6] hover:underline">{{ $verification->website }}</a>
                                            </div>
                                        @endif
                                        @if($verification->address)
                                            <div>
                                                <p class="text-[#6B6F7A]">Alamat:</p>
                                                <p class="font-medium text-[#23252F]">{{ $verification->address }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($verification->status === 'rejected' && $verification->rejection_reason)
                                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <p class="text-[12px] text-red-800">
                                                <strong>Alasan ditolak:</strong> {{ $verification->rejection_reason }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($verification->verified_at)
                                        <p class="text-[11px] text-[#6B6F7A] mt-3">
                                            @if($verification->status === 'approved')
                                                Diverifikasi pada {{ $verification->verified_at->format('d M Y H:i') }}
                                            @else
                                                Ditinjau pada {{ $verification->verified_at->format('d M Y H:i') }}
                                            @endif
                                        </p>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('organization.show', $verification) }}"
                                    class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-[12px] font-medium hover:bg-gray-200 transition-colors ml-4"
                                >
                                    Detail
                                </a>
                            </div>
                        </section>
                    @endforeach
                </div>

                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-[12px] text-blue-800">
                        <strong>Info:</strong> Anda dapat memiliki maksimal 3 organisasi terverifikasi. 
                        Setelah terverifikasi, organisasi dapat digunakan saat membuat kampanye untuk menampilkan badge "Terverifikasi".
                    </p>
                </div>
            @endif
        </main>
    </body>
</html>

