<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $mode === 'register' ? 'Daftar' : 'Masuk' }} - EduSorong</title>

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
        <main class="min-h-screen flex items-center justify-center px-4 py-10">
            <div class="max-w-5xl w-full bg-white rounded-[18px] shadow-[0_18px_60px_rgba(0,0,0,0.16)] overflow-hidden grid md:grid-cols-2">
                {{-- Left panel --}}
                <div class="bg-[#2E3242] text-white px-10 py-10 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="flex items-center gap-2 text-[18px] font-semibold">
                            <x-lucide-graduation-cap class="w-7 h-7 text-[#9DAE81]" />
                            <span>EduSorong</span>
                        </div>

                        <div class="space-y-4 mt-8">
                            <h1 class="text-[28px] font-semibold leading-tight">
                                Bergabunglah<br />
                                dalam Gerakan<br />
                                Kebaikan
                            </h1>
                            <p class="text-[13px] text-[#D2D6E0] leading-relaxed">
                                Ribuan anak menunggu uluran tangan Anda untuk masa depan yang lebih cerah.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-[11px] text-[#D2D6E0] mt-8">
                        <x-lucide-shield-check class="w-4 h-4 text-[#9DAE81]" />
                        <span>100% Data Aman &amp; Terverifikasi</span>
                    </div>
                </div>

                {{-- Right panel --}}
                @php
                    $initialMode = $mode === 'register' ? 'register' : 'login';
                @endphp
                <div class="px-10 py-10 bg-white" id="auth-tabs-root" data-initial-mode="{{ $initialMode }}">
                    {{-- Tabs --}}
                    <div class="flex items-center gap-8 text-[15px] mb-8 border-b border-[#E0E3F0]">
                        <button
                            type="button"
                            data-auth-tab="login"
                            class="pb-2 border-b-2 {{ $initialMode === 'login' ? 'text-[#2E3242] font-semibold border-[#2E3242]' : 'border-transparent text-[#B5B7C0]' }}"
                        >
                            Masuk
                        </button>
                        <button
                            type="button"
                            data-auth-tab="register"
                            class="pb-2 border-b-2 {{ $initialMode === 'register' ? 'text-[#2E3242] font-semibold border-[#2E3242]' : 'border-transparent text-[#B5B7C0]' }}"
                        >
                            Daftar
                        </button>
                    </div>

                    {{-- Register form --}}
                    <div data-auth-panel="register" class="{{ $initialMode === 'register' ? '' : 'hidden' }}">
                        <form action="{{ route('auth.register') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="space-y-1 text-[13px]">
                                <label for="name" class="font-medium text-[#2E3242]">Nama Lengkap</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    required
                                    class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                                />
                                @error('name')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1 text-[13px]">
                                <label for="email" class="font-medium text-[#2E3242]">Email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                                />
                                @error('email')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1 text-[13px]">
                                <label for="password" class="font-medium text-[#2E3242]">Password</label>
                                <div
                                    class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 flex items-center gap-2 focus-within:ring-2 focus-within:ring-[#9DAE81]"
                                >
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        required
                                        class="flex-1 border-none outline-none text-[13px] bg-transparent"
                                    />
                                    <button
                                        type="button"
                                        class="text-[#8C8F99]"
                                        data-toggle-password="password"
                                    >
                                        <x-lucide-eye class="w-4 h-4" data-eye-open />
                                        <x-lucide-eye-off class="w-4 h-4 hidden" data-eye-closed />
                                    </button>
                                </div>
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-1 text-[13px]">
                                <label for="password_confirmation" class="font-medium text-[#2E3242]">
                                    Konfirmasi Password
                                </label>
                                <div
                                    class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 flex items-center gap-2 focus-within:ring-2 focus-within:ring-[#9DAE81]"
                                >
                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        required
                                        class="flex-1 border-none outline-none text-[13px] bg-transparent"
                                    />
                                    <button
                                        type="button"
                                        class="text-[#8C8F99]"
                                        data-toggle-password="password_confirmation"
                                    >
                                        <x-lucide-eye class="w-4 h-4" data-eye-open />
                                        <x-lucide-eye-off class="w-4 h-4 hidden" data-eye-closed />
                                    </button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="mt-4 h-10 w-full rounded-[10px] bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                            >
                                Daftar Akun
                            </button>
                        </form>
                    </div>

                    {{-- Login form --}}
                    <div data-auth-panel="login" class="{{ $initialMode === 'login' ? '' : 'hidden' }}">
                        <form action="{{ route('auth.login') }}" method="POST" class="space-y-4">
                            @csrf

                            @if ($errors->has('email'))
                                <p class="text-xs text-red-500 mb-1">
                                    {{ $errors->first('email') }}
                                </p>
                            @endif

                            <div class="space-y-1 text-[13px]">
                                <label for="login_email" class="font-medium text-[#2E3242]">Email</label>
                                <input
                                    id="login_email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                                />
                            </div>

                            <div class="space-y-1 text-[13px]">
                                <label for="login_password" class="font-medium text-[#2E3242]">Password</label>
                                <div
                                    class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 flex items-center gap-2 focus-within:ring-2 focus-within:ring-[#9DAE81]"
                                >
                                    <input
                                        id="login_password"
                                        name="password"
                                        type="password"
                                        required
                                        class="flex-1 border-none outline-none text-[13px] bg-transparent"
                                    />
                                    <button
                                        type="button"
                                        class="text-[#8C8F99]"
                                        data-toggle-password="login_password"
                                    >
                                        <x-lucide-eye class="w-4 h-4" data-eye-open />
                                        <x-lucide-eye-off class="w-4 h-4 hidden" data-eye-closed />
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-[#8C8F99]">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="remember" class="rounded border-[#D5D8E2]" />
                                    <span>Ingat saya</span>
                                </label>
                                <button type="button" class="hover:text-[#2E3242]">Lupa Password?</button>
                            </div>

                            <button
                                type="submit"
                                class="mt-4 h-10 w-full rounded-[10px] bg-[#2E3242] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#232632] transition-colors"
                            >
                                Masuk Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>


