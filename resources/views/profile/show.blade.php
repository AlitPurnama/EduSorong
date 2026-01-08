@php
    $isOwnProfile = Auth::check() && Auth::id() === $user->id;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $isOwnProfile ? 'Profil Saya' : 'Profil ' . $user->name }} - EduSorong</title>

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
        <div class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)]">
            <div class="p-6 border-b border-[#E7E0B8]">
                <h1 class="text-[28px] font-semibold">{{ $isOwnProfile ? 'Profil Saya' : 'Profil Publik' }}</h1>
            </div>

            <div class="p-6 space-y-6">
                {{-- Profile Header --}}
                <div class="flex items-center gap-6">
                    <div class="relative">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto Profil" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                        @else
                            <div class="w-24 h-24 rounded-full bg-[#2E3242] text-white flex items-center justify-center text-[36px] font-semibold border-4 border-white shadow-md">
                                <span>{{ strtoupper(mb_substr($user->name ?: $user->email, 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-2xl font-bold text-[#2E3242]">{{ $user->name }}</h2>
                        <p class="text-[15px] text-[#6B6F7A]">
                            Bergabung sejak <span data-utc-time="{{ $user->created_at->toIso8601String() }}" data-format="date-only">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                        </p>
                        @if($user->ktp_verified)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-medium border border-green-200">
                                <x-lucide-check-circle class="w-3.5 h-3.5" />
                                Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium border border-yellow-200">
                                <x-lucide-x-circle class="w-3.5 h-3.5" />
                                Belum Terverifikasi
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Profile Details --}}
                <div class="grid md:grid-cols-2 gap-x-8 gap-y-4 text-[15px] pt-4">
                    @if($isOwnProfile)
                        <div>
                            <p class="text-xs text-[#8C8F99] uppercase tracking-wider">Email</p>
                            <p class="font-medium text-[#2E3242]">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-[#8C8F99] uppercase tracking-wider">No. Handphone</p>
                            <p class="font-medium text-[#2E3242]">{{ $user->phone ?: '-' }}</p>
                        </div>
                    @endif
                    <div class="md:col-span-2">
                        <p class="text-xs text-[#8C8F99] uppercase tracking-wider">Bio</p>
                        <p class="font-medium text-[#2E3242] whitespace-pre-wrap">{{ $user->bio ?: 'Pengguna ini belum menulis bio.' }}</p>
                    </div>
                </div>

                {{-- Actions --}}
                @if($isOwnProfile)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                         <a
                            href="{{ route('settings.show') }}"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                        >
                            <x-lucide-pencil class="w-4 h-4" />
                            <span>Ubah Profil</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
