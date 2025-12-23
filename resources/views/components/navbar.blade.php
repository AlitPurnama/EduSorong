@props([
    // 'active' can be: 'beranda', 'kampanye', 'cara-kerja', 'tentang-kami'
    'active' => null,
])

@php
    $linkBaseClasses = 'hover:text-[#343742] transition-colors';
    $isActive = fn (string $name) => $active === $name ? 'text-[#343742] font-semibold' : 'text-[#8C8F99]';
@endphp

<header class="border-b border-[#efe6be] bg-[#FFF7D6]">
    <div class="max-w-5xl mx-auto px-4 lg:px-4 flex items-center justify-between h-[60px] text-[13px]">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-full flex items-center justify-center">
                <x-lucide-graduation-cap class="w-7 h-7 text-[#9DAE81]" />
            </div>
            <span class="text-[19px] font-semibold tracking-tight text-[#343742]">
                EduSorong
            </span>
        </a>

        {{-- Nav links --}}
        <nav class="hidden md:flex items-center gap-9 text-[15px]">
            <a href="{{ url('/') }}" class="{{ $linkBaseClasses }} {{ $isActive('beranda') }}">
                Beranda
            </a>
            <a href="{{ url('/kampanye') }}" class="{{ $linkBaseClasses }} {{ $isActive('kampanye') }}">
                Kampanye
            </a>
            <a href="#cara-kerja" class="{{ $linkBaseClasses }} {{ $isActive('cara-kerja') }}">
                Cara Kerja
            </a>
            <a href="#tentang-kami" class="{{ $linkBaseClasses }} {{ $isActive('tentang-kami') }}">
                Tentang Kami
            </a>
        </nav>

        {{-- Auth actions --}}
        @auth
            @php
                $user = auth()->user();
                $initial = strtoupper(mb_substr($user->name ?: $user->email, 0, 1));
            @endphp
            <div class="flex items-center gap-4 text-[15px]">
                <div class="relative" data-profile-menu>
                    <button
                        type="button"
                        class="w-9 h-9 rounded-full bg-[#2E3242] text-white flex items-center justify-center text-[14px] font-semibold shadow-[0_2px_0_rgba(0,0,0,0.22)] hover:bg-[#262934] transition-colors overflow-hidden"
                        data-profile-trigger
                    >
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <span>{{ $initial }}</span>
                        @endif
                    </button>
                    <div
                        class="hidden absolute right-0 mt-2 w-40 rounded-xl bg-white border border-[#E0E3F0] shadow-[0_12px_30px_rgba(0,0,0,0.18)] py-2 text-[13px] z-20"
                        data-profile-dropdown
                    >
                        <a
                            href="{{ route('dashboard') }}"
                            class="block px-4 py-2 hover:bg-[#F5F5FB] text-[#23252F]"
                        >
                            Dashboard
                        </a>
                        <a
                            href="{{ route('settings.show') }}"
                            class="block px-4 py-2 hover:bg-[#F5F5FB] text-[#23252F]"
                        >
                            Pengaturan
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left px-4 py-2 hover:bg-[#F5F5FB] text-[#E11D48]"
                            >
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="flex items-center gap-4 text-[15px]">
                <a href="{{ route('login') }}" class="text-[#343742] font-medium hover:text-black transition-colors">
                    Masuk
                </a>
                <a
                    href="{{ route('register') }}"
                    class="px-5 py-1.5 rounded-full bg-[#343742] text-white font-semibold shadow-[0_2px_0_rgba(0,0,0,0.22)] hover:bg-[#262934] transition-colors"
                >
                    Daftar
                </a>
            </div>
        @endauth
    </div>
</header>


