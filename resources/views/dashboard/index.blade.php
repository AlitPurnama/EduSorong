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
                <a
                    href="{{ route('dashboard.campaigns.create') }}"
                    class="px-4 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                >
                    + Buat Kampanye
                </a>
            </div>

            @if (session('status'))
                <div class="px-4 py-2 rounded-md bg-[#ECFDF3] text-[13px] text-[#166534] border border-[#BBF7D0]">
                    {{ session('status') }}
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

                                    <form
                                        action="{{ route('dashboard.campaigns.destroy', $campaign) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kampanye ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="text-[12px] text-red-500 hover:text-red-600"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>


