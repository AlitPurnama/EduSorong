<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Detail Organisasi - EduSorong</title>

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

        <main class="max-w-3xl mx-auto px-4 lg:px-4 pt-10 pb-16 space-y-8">
            <div>
                <a href="{{ route('organization.index') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-2 inline-block">
                    ← Kembali ke Daftar Organisasi
                </a>
                <h1 class="text-[22px] font-semibold">Detail Organisasi/Yayasan</h1>
            </div>

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6 space-y-4">
                <div class="flex items-center gap-2 pb-4 border-b border-[#E7E0B8]">
                    <h3 class="text-[16px] font-semibold text-[#23252F]">{{ $organizationVerification->organization_name }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                        @if($organizationVerification->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($organizationVerification->status === 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($organizationVerification->status) }}
                    </span>
                </div>

                @if($organizationVerification->organization_description)
                    <div>
                        <p class="text-[13px] font-medium text-[#23252F] mb-2">Deskripsi</p>
                        <p class="text-[13px] text-[#50545F] leading-relaxed">{{ $organizationVerification->organization_description }}</p>
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-4">
                    @if($organizationVerification->npwp)
                        <div>
                            <p class="text-[12px] text-[#6B6F7A] mb-1">NPWP</p>
                            <p class="text-[14px] font-medium text-[#23252F]">{{ $organizationVerification->npwp }}</p>
                        </div>
                    @endif

                    @if($organizationVerification->phone)
                        <div>
                            <p class="text-[12px] text-[#6B6F7A] mb-1">Nomor Telepon</p>
                            <p class="text-[14px] font-medium text-[#23252F]">{{ $organizationVerification->phone }}</p>
                        </div>
                    @endif

                    @if($organizationVerification->website)
                        <div>
                            <p class="text-[12px] text-[#6B6F7A] mb-1">Website</p>
                            <a href="{{ $organizationVerification->website }}" target="_blank" class="text-[14px] font-medium text-[#3B82F6] hover:underline">
                                {{ $organizationVerification->website }}
                            </a>
                        </div>
                    @endif

                    @if($organizationVerification->address)
                        <div class="md:col-span-2">
                            <p class="text-[12px] text-[#6B6F7A] mb-1">Alamat</p>
                            <p class="text-[14px] font-medium text-[#23252F]">{{ $organizationVerification->address }}</p>
                        </div>
                    @endif
                </div>

                @if($organizationVerification->document_path)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <p class="text-[13px] font-medium text-[#23252F] mb-2">Dokumen Verifikasi</p>
                        <a 
                            href="{{ asset('storage/' . $organizationVerification->document_path) }}" 
                            target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-[#23252F] text-[13px] font-medium hover:bg-gray-200 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Lihat Dokumen
                        </a>
                    </div>
                @endif

                @if($organizationVerification->status === 'rejected' && $organizationVerification->rejection_reason)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-[13px] font-medium text-red-800 mb-1">Alasan Penolakan</p>
                            <p class="text-[13px] text-red-700">{{ $organizationVerification->rejection_reason }}</p>
                        </div>
                    </div>
                @endif

                @if($organizationVerification->verified_at)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <p class="text-[12px] text-[#6B6F7A]">
                            @if($organizationVerification->status === 'approved')
                                Diverifikasi oleh <strong>{{ $organizationVerification->verifier->name ?? 'Admin' }}</strong> 
                                pada {{ $organizationVerification->verified_at->format('d M Y H:i') }}
                            @elseif($organizationVerification->status === 'rejected')
                                Ditinjau oleh <strong>{{ $organizationVerification->verifier->name ?? 'Admin' }}</strong> 
                                pada {{ $organizationVerification->verified_at->format('d M Y H:i') }}
                            @endif
                        </p>
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>

