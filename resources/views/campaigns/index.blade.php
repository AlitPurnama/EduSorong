<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Jelajahi Semua Kampanye - EduSorong</title>

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
        <x-navbar active="kampanye" />

        <main class="max-w-5xl mx-auto px-4 lg:px-4 pt-10 pb-16 space-y-10">
            {{-- Header filter section --}}
            <section class="rounded-[24px] bg-[#2E3242] px-6 md:px-10 py-10 text-white text-center space-y-6">
                <div class="space-y-2">
                    <h1 class="text-[26px] md:text-[30px] font-semibold tracking-tight">
                        Jelajahi Semua Kampanye
                    </h1>
                    <p class="text-[13px] md:text-[14px] text-[#D4D7E5] max-w-2xl mx-auto leading-relaxed">
                        Temukan siswa yang membutuhkan bantuan di sekitarmu dan jadilah bagian dari perubahan masa depan
                        mereka.
                    </p>
                </div>

                <div class="bg-[#2E3242] rounded-full border border-[#1f2432] px-4 py-2">
                    <div class="grid gap-4 text-left md:grid-cols-[2fr_1fr_1fr]">
                        <input
                            type="text"
                            placeholder="Cari nama siswa atau kampanye"
                            class="h-11 rounded-full px-4 text-[13px] text-[#23252F] bg-white border border-[#D0D3DD] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                        />
                        <input
                            type="text"
                            placeholder="Lokasi"
                            class="h-11 rounded-full px-4 text-[13px] text-[#23252F] bg-white border border-[#D0D3DD] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                        />
                        <input
                            type="text"
                            placeholder="Jenjang"
                            class="h-11 rounded-full px-4 text-[13px] text-[#23252F] bg-white border border-[#D0D3DD] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                        />
                    </div>
                </div>
            </section>

            {{-- Campaign grid --}}
            <section class="space-y-6">
                <div class="grid md:grid-cols-3 gap-6">
                    @forelse ($campaigns as $campaign)
                        <x-campaign-card
                            :href="route('campaigns.show', $campaign)"
                            :location="$campaign->location ?? 'Sorong Utara'"
                            :title="$campaign->title"
                            :raised="$campaign->raised_amount"
                            :target="$campaign->target_amount"
                            :organization="$campaign->organization ?? 'Nama Yayasan'"
                            :image="$campaign->image_path ? asset('storage/' . $campaign->image_path) : null"
                        />
                    @empty
                        @for ($i = 0; $i < 6; $i++)
                            <x-campaign-card />
                        @endfor
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="flex justify-center gap-2 pt-4 text-[13px]">
                    {{ $campaigns->links() }}
                </div>
            </section>
        </main>
    </body>
</html>


