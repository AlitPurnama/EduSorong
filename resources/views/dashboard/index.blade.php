<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Dashboard - EduSorong</title>

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

        <main class="max-w-5xl mx-auto px-4 lg:px-4 pt-10 pb-16 space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-[22px] font-semibold">Dashboard</h1>
                    <p class="text-[13px] text-[#6B6F7A]">
                        Kelola kampanye yang Anda buat.
                    </p>
                </div>
                @if($user->ktp_verification_status === 'approved')
                    <a
                        href="{{ route('dashboard.campaigns.create') }}"
                        class="px-4 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                    >
                        + Buat Kampanye
                    </a>
                @else
                    <a
                        href="{{ route('settings.ktp.show') }}"
                        class="px-4 py-2 rounded-full bg-yellow-500 text-white text-[13px] font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-yellow-600 transition-colors"
                    >
                        Verifikasi KTP
                    </a>
                @endif
            </div>

            @if (session('status'))
                <div class="px-4 py-2 rounded-md bg-[#ECFDF3] text-[13px] text-[#166534] border border-[#BBF7D0]">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="px-4 py-2 rounded-md bg-[#FEF2F2] text-[13px] text-[#991B1B] border border-[#FECACA]">
                    {{ session('error') }}
                </div>
            @endif

            @if($user->ktp_verification_status !== 'approved')
                <div class="px-4 py-3 rounded-md bg-yellow-50 text-[13px] text-yellow-800 border border-yellow-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-medium mb-1">Verifikasi KTP Diperlukan</p>
                            <p class="text-[12px] text-yellow-700">
                                Anda harus verifikasi KTP terlebih dahulu sebelum dapat membuat kampanye. 
                                <a href="{{ route('settings.ktp.show') }}" class="underline font-medium">Klik di sini untuk verifikasi KTP</a>.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                @if ($campaigns->isEmpty())
                    <p class="text-[13px] text-[#6B6F7A]">
                        Anda belum memiliki kampanye. Klik tombol "Buat Kampanye" untuk memulai.
                    </p>
                @else
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach ($campaigns as $campaign)
                            <div class="border border-[#E7E0B8] rounded-xl p-4 text-[13px] space-y-2 bg-[#FFFEFB]">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] text-[#8C8F99] uppercase tracking-[0.12em]">
                                            {{ $campaign->location ?? 'Lokasi tidak diketahui' }}
                                        </p>
                                        <h2 class="text-[15px] font-semibold text-[#23252F]">
                                            {{ $campaign->title }}
                                        </h2>
                                    </div>
                                    <span class="text-[11px] text-[#8C8F99]">
                                        Rp {{ number_format($campaign->raised_amount, 0, ',', '.') }} /
                                        Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="h-1.5 rounded-full bg-[#ECE6C3] overflow-hidden">
                                    @php
                                        $progress =
                                            $campaign->target_amount > 0
                                                ? min(100, ($campaign->raised_amount / $campaign->target_amount) * 100)
                                                : 0;
                                    @endphp
                                    <div class="h-full rounded-full bg-[#9DAE81]" style="width: {{ $progress }}%"></div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <a
                                        href="{{ route('campaigns.show', $campaign) }}"
                                        class="text-[12px] text-[#2E3242] underline underline-offset-2"
                                    >
                                        Lihat detail
                                    </a>

                                    @php
                                        $hasDeletionRequest = $campaign->deletionRequests()->where('status', 'pending')->exists();
                                    @endphp
                                    
                                    @if($hasDeletionRequest)
                                        <span class="text-[12px] text-yellow-600 font-medium">
                                            Menunggu Review
                                        </span>
                                    @else
                                        <a
                                            href="{{ route('dashboard.campaigns.request-deletion', $campaign) }}"
                                            class="text-[12px] text-red-500 hover:text-red-600"
                                        >
                                            Hapus
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>


