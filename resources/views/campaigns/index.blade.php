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

                <form method="GET" action="{{ route('campaigns.index') }}" class="bg-[#2E3242] rounded-full border border-[#1f2432] px-4 py-2">
                    <div class="flex gap-3 items-center">
                        <div class="flex-1 relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari berdasarkan judul atau lokasi kampanye"
                                class="w-full h-11 rounded-full px-4 pl-12 text-[13px] text-[#23252F] bg-white border border-[#D0D3DD] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                            />
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[#6B6F7A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button
                            type="submit"
                            class="h-11 px-6 rounded-full bg-[#9DAE81] text-white text-[13px] font-medium hover:bg-[#8FA171] transition-colors whitespace-nowrap"
                        >
                            Cari
                        </button>
                        @if(request('search'))
                            <a
                                href="{{ route('campaigns.index') }}"
                                class="h-11 px-4 rounded-full bg-white/10 text-white text-[13px] font-medium hover:bg-white/20 transition-colors whitespace-nowrap flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            {{-- Campaign grid --}}
            <section class="space-y-6">
                @if(request('search'))
                    <div class="text-[13px] text-[#6B6F7A]">
                        Menampilkan hasil pencarian untuk "<span class="font-medium text-[#23252F]">{{ request('search') }}</span>"
                        @if($campaigns->total() > 0)
                            ({{ $campaigns->total() }} kampanye ditemukan)
                        @endif
                    </div>
                @endif

                <div class="grid md:grid-cols-3 gap-6">
                    @forelse ($campaigns as $campaign)
                        <x-campaign-card
                            :href="route('campaigns.show', $campaign)"
                            :location="$campaign->location ?? 'Sorong Utara'"
                            :title="$campaign->title"
                            :raised="$campaign->raised_amount"
                            :target="$campaign->target_amount"
                            :organization="$campaign->organization_name"
                            :image="$campaign->image_path ? asset('storage/' . $campaign->image_path) : null"
                            :isVerified="$campaign->hasVerifiedOrganization()"
                        />
                    @empty
                        <div class="col-span-3 text-center py-12">
                            <div class="space-y-3">
                                <svg class="w-16 h-16 mx-auto text-[#D5D8E2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-[14px] text-[#6B6F7A]">
                                    @if(request('search'))
                                        Tidak ada kampanye yang ditemukan untuk "{{ request('search') }}"
                                    @else
                                        Belum ada kampanye yang tersedia
                                    @endif
                                </p>
                                @if(request('search'))
                                    <a href="{{ route('campaigns.index') }}" class="inline-block text-[13px] text-[#9DAE81] hover:underline">
                                        Lihat semua kampanye
                                    </a>
                                @endif
                            </div>
                        </div>
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


