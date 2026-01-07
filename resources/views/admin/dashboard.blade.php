<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Admin Dashboard - EduSorong</title>

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
                    <h1 class="text-[22px] font-semibold">Admin Dashboard</h1>
                    <p class="text-[13px] text-[#6B6F7A]">
                        Kelola verifikasi yayasan dan pencairan dana.
                    </p>
                </div>
            </div>

            @if (session('success'))
                <div class="px-4 py-2 rounded-md bg-[#ECFDF3] text-[13px] text-[#166534] border border-[#BBF7D0]">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="px-4 py-2 rounded-md bg-[#FEF2F2] text-[13px] text-[#991B1B] border border-[#FECACA]">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid md:grid-cols-2 gap-4">
                <a href="{{ route('admin.verifications') }}" class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6 hover:shadow-[0_12px_26px_rgba(0,0,0,0.12)] transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[13px] text-[#6B6F7A] mb-1">Verifikasi Yayasan</p>
                            <p class="text-[28px] font-semibold text-[#23252F]">{{ $stats['pending_verifications'] }}</p>
                            <p class="text-[12px] text-[#6B6F7A] mt-1">Menunggu Review</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#FEF3C7] flex items-center justify-center">
                            <x-lucide-shield-check class="w-6 h-6 text-[#F59E0B]" />
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.ktp-verifications') }}" class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6 hover:shadow-[0_12px_26px_rgba(0,0,0,0.12)] transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[13px] text-[#6B6F7A] mb-1">Verifikasi KTP</p>
                            <p class="text-[28px] font-semibold text-[#23252F]">{{ $stats['pending_ktp_verifications'] }}</p>
                            <p class="text-[12px] text-[#6B6F7A] mt-1">Menunggu Review</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#FEE2E2] flex items-center justify-center">
                            <x-lucide-credit-card class="w-6 h-6 text-[#EF4444]" />
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.withdrawals') }}" class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6 hover:shadow-[0_12px_26px_rgba(0,0,0,0.12)] transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[13px] text-[#6B6F7A] mb-1">Request Pencairan</p>
                            <p class="text-[28px] font-semibold text-[#23252F]">{{ $stats['pending_withdrawals'] }}</p>
                            <p class="text-[12px] text-[#6B6F7A] mt-1">Menunggu Review</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#DBEAFE] flex items-center justify-center">
                            <x-lucide-banknote class="w-6 h-6 text-[#3B82F6]" />
                        </div>
                    </div>
                </a>

                <div class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[13px] text-[#6B6F7A] mb-1">Total Kampanye</p>
                            <p class="text-[28px] font-semibold text-[#23252F]">{{ $stats['total_campaigns'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#F3E8FF] flex items-center justify-center">
                            <x-lucide-heart class="w-6 h-6 text-[#9333EA]" />
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[13px] text-[#6B6F7A] mb-1">Total Pengguna</p>
                            <p class="text-[28px] font-semibold text-[#23252F]">{{ $stats['total_users'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full bg-[#D1FAE5] flex items-center justify-center">
                            <x-lucide-users class="w-6 h-6 text-[#10B981]" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                <h2 class="text-[16px] font-semibold mb-4">Quick Actions</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.verifications') }}" class="px-4 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-medium hover:bg-[#8FA171] transition-colors">
                        Review Verifikasi Yayasan
                    </a>
                    <a href="{{ route('admin.ktp-verifications') }}" class="px-4 py-2 rounded-full bg-[#EF4444] text-white text-[13px] font-medium hover:bg-[#DC2626] transition-colors">
                        Review Verifikasi KTP
                    </a>
                    <a href="{{ route('admin.withdrawals') }}" class="px-4 py-2 rounded-full bg-[#3B82F6] text-white text-[13px] font-medium hover:bg-[#2563EB] transition-colors">
                        Review Request Pencairan
                    </a>
                    <a href="{{ route('admin.deletion-requests') }}" class="px-4 py-2 rounded-full bg-[#DC2626] text-white text-[13px] font-medium hover:bg-[#B91C1C] transition-colors">
                        Review Request Penghapusan
                    </a>
                </div>
            </section>
        </main>
    </body>
</html>

