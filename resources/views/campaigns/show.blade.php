<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $campaign->title }} - EduSorong</title>

        <!-- Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    </head>
    <body class="bg-[#FFF7D6] text-[#23252F] font-sans text-[14px]">
        <x-navbar />

        <main class="max-w-4xl mx-auto px-4 lg:px-0 pt-10 pb-16 space-y-8">
            <a href="{{ route('campaigns.index') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F]">
                &larr; Kembali ke semua kampanye
            </a>

            <section class="grid md:grid-cols-[1.1fr_minmax(0,1fr)] gap-8 items-start">
                <div class="space-y-4">
                    <img
                        src="{{ $campaign->image_path ? asset('storage/' . $campaign->image_path) : asset('images/placeholder.jpg') }}"
                        alt="Gambar kampanye"
                        class="w-full h-[220px] rounded-2xl object-cover border border-[#E7E0B8] shadow-[0_10px_24px_rgba(0,0,0,0.12)] mb-4"
                    />
                    <p class="inline-flex items-center gap-2 text-[12px] text-[#6B6F7A]">
                        <span class="w-2 h-2 rounded-full bg-[#9DAE81]"></span>
                        {{ $campaign->location ?? 'Sorong Utara' }}
                    </p>
                    <h1 class="text-[26px] md:text-[30px] font-semibold leading-tight">
                        {{ $campaign->title }}
                    </h1>
                        <p class="text-[13px] text-[#50545F] leading-relaxed">
                        {{ $campaign->excerpt ?? 'Deskripsi singkat kampanye akan ditampilkan di sini.' }}
                    </p>
                </div>

                <div class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-5 space-y-4">
                    <div class="space-y-1 text-[13px]">
                        <div class="flex items-center justify-between text-[#6B6F7A]">
                            <span>Terkumpul</span>
                            <span>Target</span>
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
                        <div class="flex items-center justify-between font-medium text-[#23252F]">
                            <span>Rp {{ number_format($campaign->raised_amount, 0, ',', '.') }}</span>
                            <span>Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="text-[13px] text-[#6B6F7A]">
                        <p>Dikelola oleh</p>
                        <p class="font-medium text-[#23252F]">
                            {{ $campaign->organization ?? 'Nama Yayasan' }}
                        </p>
                    </div>

                    <div class="text-[13px] text-[#6B6F7A]">
                        <p>Dibuat oleh</p>
                        <a href="{{ route('users.show', $campaign->user) }}" class="font-medium text-[#23252F] hover:underline">
                            {{ $campaign->user->name }}
                        </a>
                    </div>

                    <div class="pt-4 border-t border-[#E7E0B8] space-y-4">
                        <p class="text-[13px] font-medium text-[#23252F]">Pilih Nominal Donasi</p>

                        <div class="grid grid-cols-3 gap-2">
                            <button
                                type="button"
                                data-donation-amount="20000"
                                class="donation-option px-3 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[13px] font-medium text-[#23252F] hover:bg-[#F5F5FB] hover:border-[#9DAE81] transition-colors"
                            >
                                Rp 20.000
                            </button>
                            <button
                                type="button"
                                data-donation-amount="50000"
                                class="donation-option px-3 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[13px] font-medium text-[#23252F] hover:bg-[#F5F5FB] hover:border-[#9DAE81] transition-colors"
                            >
                                Rp 50.000
                            </button>
                            <button
                                type="button"
                                data-donation-amount="100000"
                                class="donation-option px-3 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[13px] font-medium text-[#23252F] hover:bg-[#F5F5FB] hover:border-[#9DAE81] transition-colors"
                            >
                                Rp 100.000
                            </button>
                        </div>

                        <div>
                            <label for="custom_amount" class="block text-[13px] font-medium text-[#23252F] mb-2">Atau masukkan nominal lain</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#6B6F7A] text-[13px]">Rp</span>
                                <input
                                    type="number"
                                    id="custom_amount"
                                    name="custom_amount"
                                    placeholder="Minimal 10.000"
                                    min="10000"
                                    step="1000"
                                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                >
                            </div>
                            <p class="text-[11px] text-[#6B6F7A] mt-1">Minimal donasi: Rp 10.000</p>
                        </div>

                        <button
                            type="button"
                            id="donate_button"
                            disabled
                            class="mt-2 h-10 w-full rounded-full bg-[#9DAE81]/50 text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.08)] cursor-not-allowed transition-colors"
                        >
                            Donasi Sekarang
                        </button>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6 text-[14px] leading-relaxed text-[#50545F]">
                {!! nl2br(e($campaign->description ?? 'Belum ada deskripsi lengkap untuk kampanye ini.')) !!}
            </section>
        </main>
    </body>
</html>
