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
        
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body class="bg-[#FFF7D6] text-[#23252F] font-sans text-[14px]">
        <x-navbar />

        <main class="max-w-4xl mx-auto px-4 lg:px-0 pt-10 pb-16 space-y-8" data-campaign-id="{{ $campaign->id }}" @auth data-is-authenticated="true" @endauth>
            <a href="{{ route('campaigns.index') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F]">
                &larr; Kembali ke semua kampanye
            </a>

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
                        <div class="flex items-center gap-2">
                            <p>Dikelola oleh</p>
                            @if($campaign->hasVerifiedOrganization())
                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-[11px] font-medium">
                                    ✓ Terverifikasi
                                </span>
                            @endif
                        </div>
                        <p class="font-medium text-[#23252F]">
                            {{ $campaign->organization_name }}
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

            {{-- Request Withdrawal Section (for campaign owner) --}}
            @auth
                @if(auth()->id() === $campaign->user_id && $campaign->canRequestWithdrawal())
                    @php
                        $existingRequest = \App\Models\WithdrawalRequest::where('campaign_id', $campaign->id)
                            ->whereIn('status', ['pending', 'approved'])
                            ->first();
                    @endphp
                    @if(!$existingRequest)
                        <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-[16px] font-semibold text-[#23252F] mb-2">Request Pencairan Dana</h3>
                                    <p class="text-[13px] text-[#6B6F7A] mb-3">
                                        Kampanye Anda sudah mencapai {{ number_format($campaign->progress_percentage, 1) }}% dari target. 
                                        Anda dapat mengajukan pencairan dana yang terkumpul.
                                    </p>
                                    <p class="text-[12px] text-[#6B6F7A]">
                                        Dana tersedia: <strong class="text-[#23252F]">Rp {{ number_format($campaign->raised_amount, 0, ',', '.') }}</strong>
                                    </p>
                                </div>
                                <a
                                    href="{{ route('withdrawal.create', $campaign) }}"
                                    class="px-5 py-2.5 rounded-full bg-[#3B82F6] text-white text-[13px] font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#2563EB] transition-colors whitespace-nowrap"
                                >
                                    Request Pencairan
                                </a>
                            </div>
                        </section>
                    @else
                        <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <h3 class="text-[16px] font-semibold text-[#23252F] mb-2">Status Request Pencairan</h3>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                            @if($existingRequest->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($existingRequest->status === 'approved') bg-blue-100 text-blue-800
                                            @elseif($existingRequest->status === 'completed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($existingRequest->status) }}
                                        </span>
                                    </div>
                                    <p class="text-[13px] text-[#6B6F7A] mb-1">
                                        Jumlah: <strong>{{ $existingRequest->formatted_amount }}</strong>
                                    </p>
                                    @if($existingRequest->status === 'rejected' && $existingRequest->rejection_reason)
                                        <p class="text-[12px] text-red-700 mt-2">
                                            <strong>Alasan ditolak:</strong> {{ $existingRequest->rejection_reason }}
                                        </p>
                                    @endif
                                </div>
                                <a
                                    href="{{ route('withdrawal.show', $existingRequest) }}"
                                    class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-[12px] font-medium hover:bg-gray-200 transition-colors"
                                >
                                    Lihat Detail
                                </a>
                            </div>
                        </section>
                    @endif
                @endif
            @endauth

            {{-- Transparency Report Section --}}
            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                <h2 class="text-[20px] font-semibold mb-6">Laporan Transparansi</h2>

                {{-- Financial Summary --}}
                <div class="mb-6 p-4 bg-[#F5F5FB] rounded-lg">
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-[12px] text-[#6B6F7A] mb-1">Total Terkumpul</p>
                            <p class="text-[18px] font-semibold text-[#23252F]">Rp {{ number_format($campaign->raised_amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[12px] text-[#6B6F7A] mb-1">Total Dicairkan</p>
                            <p class="text-[18px] font-semibold text-[#23252F]">Rp {{ number_format($campaign->total_withdrawn, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-[12px] text-[#6B6F7A] mb-1">Sisa Dana</p>
                            <p class="text-[18px] font-semibold text-[#23252F]">Rp {{ number_format($campaign->remaining_balance, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Withdrawal History --}}
                @if($campaign->withdrawalRequests->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-[16px] font-semibold mb-4">Riwayat Pencairan Dana</h3>
                        <div class="space-y-3">
                            @foreach($campaign->withdrawalRequests as $withdrawal)
                                <div class="p-4 border border-[#E7E0B8] rounded-lg">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-[14px] font-semibold text-[#23252F]">Rp {{ number_format($withdrawal->requested_amount, 0, ',', '.') }}</p>
                                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                                    @if($withdrawal->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($withdrawal->status) }}
                                                </span>
                                            </div>
                                            @if($withdrawal->purpose)
                                                <p class="text-[13px] text-[#50545F]">{{ $withdrawal->purpose }}</p>
                                            @endif
                                            <p class="text-[12px] text-[#6B6F7A] mt-1">
                                                Dicairkan pada {{ $withdrawal->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Evidence --}}
                                    @if($withdrawal->evidences->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-[#E7E0B8]">
                                            <p class="text-[12px] font-medium text-[#23252F] mb-2">Bukti Penggunaan:</p>
                                            <div class="space-y-2">
                                                @foreach($withdrawal->evidences as $evidence)
                                                    <div class="p-3 bg-gray-50 rounded-lg">
                                                        <div class="flex items-start justify-between mb-2">
                                                            <div class="flex-1">
                                                                @if($evidence->description)
                                                                    <p class="text-[13px] text-[#23252F] mb-1">{{ $evidence->description }}</p>
                                                                @endif
                                                                <div class="flex items-center gap-2">
                                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium
                                                                        @if($evidence->status === 'verified') bg-green-100 text-green-800
                                                                        @elseif($evidence->status === 'rejected') bg-red-100 text-red-800
                                                                        @else bg-yellow-100 text-yellow-800
                                                                        @endif">
                                                                        {{ ucfirst($evidence->status) }}
                                                                    </span>
                                                                    @if($evidence->used_at)
                                                                        <span class="text-[11px] text-[#6B6F7A]">
                                                                            Digunakan: {{ $evidence->used_at->format('d M Y') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <a href="{{ $evidence->evidence_url }}" target="_blank" class="ml-3 text-[12px] text-[#3B82F6] hover:underline">
                                                                Lihat Bukti
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Campaign Updates --}}
                @if($campaign->updates->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-[16px] font-semibold mb-4">Update Progress</h3>
                        <div class="space-y-4">
                            @foreach($campaign->updates as $update)
                                <div class="p-4 border border-[#E7E0B8] rounded-lg">
                                    @if($update->title)
                                        <h4 class="text-[14px] font-semibold text-[#23252F] mb-2">{{ $update->title }}</h4>
                                    @endif
                                    <p class="text-[13px] text-[#50545F] mb-2">{{ $update->content }}</p>
                                    @if($update->image_path)
                                        <img src="{{ asset('storage/' . $update->image_path) }}" alt="Update image" class="w-full max-w-md rounded-lg mb-2">
                                    @endif
                                    <p class="text-[11px] text-[#6B6F7A]">
                                        {{ $update->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Donation History --}}
                @if($campaign->payments->count() > 0)
                    <div>
                        <h3 class="text-[16px] font-semibold mb-4">Riwayat Donasi</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-[13px]">
                                <thead class="bg-[#F5F5FB] border-b border-[#E7E0B8]">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-[#6B6F7A] font-medium">Donatur</th>
                                        <th class="px-4 py-2 text-left text-[#6B6F7A] font-medium">Jumlah</th>
                                        <th class="px-4 py-2 text-left text-[#6B6F7A] font-medium">Metode</th>
                                        <th class="px-4 py-2 text-left text-[#6B6F7A] font-medium">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($campaign->payments as $payment)
                                        <tr class="border-b border-[#E7E0B8]">
                                            <td class="px-4 py-3 text-[#23252F]">
                                                {{ $payment->donor_display_name }}
                                            </td>
                                            <td class="px-4 py-3 font-medium text-[#23252F]">
                                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-[#50545F]">
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? '-')) }}
                                            </td>
                                            <td class="px-4 py-3 text-[#6B6F7A]">
                                                {{ $payment->paid_at ? $payment->paid_at->format('d M Y') : $payment->created_at->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </section>

            {{-- Add Update Form (Only for Campaign Owner) --}}
            @auth
                @if(Auth::id() === $campaign->user_id)
                    <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                        <h2 class="text-[20px] font-semibold mb-4">Tambah Update Progress</h2>
                        <form action="{{ route('campaigns.updates.store', $campaign) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label for="update_title" class="block text-[13px] font-medium text-[#23252F] mb-2">Judul (Opsional)</label>
                                <input type="text" name="title" id="update_title" class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent">
                            </div>
                            <div>
                                <label for="update_content" class="block text-[13px] font-medium text-[#23252F] mb-2">Konten <span class="text-red-500">*</span></label>
                                <textarea name="content" id="update_content" rows="4" required class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"></textarea>
                            </div>
                            <div>
                                <label for="update_image" class="block text-[13px] font-medium text-[#23252F] mb-2">Gambar (Opsional)</label>
                                <input type="file" name="image" id="update_image" accept="image/*" class="block w-full text-[12px] text-[#6B6F7A] file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-[#9DAE81] file:text-white hover:file:bg-[#8FA171]">
                            </div>
                            <button type="submit" class="px-6 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-semibold hover:bg-[#8FA171] transition-colors">
                                Tambahkan Update
                            </button>
                        </form>
                    </section>
                @endif
            @endauth
        </main>

        <!-- Guest Donation Form Modal -->
        @guest
        <div id="guest_form_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#23252F]">Data Donatur</h2>
                    <button id="close_guest_modal" class="text-[#6B6F7A] hover:text-[#23252F]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="bg-[#F5F5FB] rounded-lg p-4 text-center">
                    <p class="text-[13px] text-[#6B6F7A]">Total Pembayaran</p>
                    <p id="guest_modal_amount_display" class="text-2xl font-bold text-[#23252F]">Rp 0</p>
                </div>

                <form id="guest_donation_form" class="space-y-4">
                    <div>
                        <label for="guest_donor_name" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="guest_donor_name"
                            name="donor_name"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Masukkan nama lengkap"
                        >
                    </div>

                    <div>
                        <label for="guest_donor_phone" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            id="guest_donor_phone"
                            name="donor_phone"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="081234567890"
                        >
                    </div>

                    <div>
                        <label for="guest_donor_email" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="guest_donor_email"
                            name="donor_email"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="email@example.com"
                        >
                    </div>

                    <div>
                        <label for="guest_donor_message" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Doa / Kata-kata (Opsional)
                        </label>
                        <textarea
                            id="guest_donor_message"
                            name="donor_message"
                            rows="3"
                            maxlength="1000"
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent resize-none"
                            placeholder="Tuliskan doa atau kata-kata untuk kampanye ini..."
                        ></textarea>
                        <p class="text-[11px] text-[#6B6F7A] mt-1">Maksimal 1000 karakter</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="guest_is_anonymous"
                            name="is_anonymous"
                            class="w-4 h-4 text-[#9DAE81] border-[#E7E0B8] rounded focus:ring-[#9DAE81]"
                        >
                        <label for="guest_is_anonymous" class="text-[13px] text-[#23252F] cursor-pointer">
                            Sembunyikan nama saya (Anonim)
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full h-10 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.08)] hover:bg-[#8FA171] transition-colors"
                    >
                        Lanjutkan ke Pembayaran
                    </button>
                </form>
            </div>
        </div>
        @endguest

        <!-- Payment Method Modal -->
        <div id="payment_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#23252F]">Pilih Metode Pembayaran</h2>
                    <button id="close_modal" class="text-[#6B6F7A] hover:text-[#23252F]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="bg-[#F5F5FB] rounded-lg p-4 text-center">
                    <p class="text-[13px] text-[#6B6F7A]">Total Pembayaran</p>
                    <p id="modal_amount_display" class="text-2xl font-bold text-[#23252F]">Rp 0</p>
                </div>

                <div class="space-y-3">
                    <!-- QRIS -->
                    <button
                        type="button"
                        data-payment-method="qris"
                        data-original-text="QRIS"
                        class="w-full flex items-center justify-between p-4 rounded-lg border-2 border-[#E7E0B8] hover:border-[#9DAE81] hover:bg-[#F5F5FB] transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#9DAE81] rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-[#23252F]">QRIS</p>
                                <p class="text-[12px] text-[#6B6F7A]">Scan QR Code untuk bayar</p>
                            </div>
                        </div>
                        <span class="button-text">Pilih</span>
                        <span class="loading-state hidden">
                            <svg class="animate-spin h-5 w-5 text-[#9DAE81]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>

                    <!-- Virtual Account Options -->
                    <!-- BCA VA -->
                    <button
                        type="button"
                        data-payment-method="virtual_account"
                        data-bank="bca"
                        data-original-text="BCA VA"
                        class="w-full flex items-center justify-between p-4 rounded-lg border-2 border-[#E7E0B8] hover:border-[#9DAE81] hover:bg-[#F5F5FB] transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#1A3A8F] rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                BCA
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-[#23252F]">BCA VA</p>
                                <p class="text-[12px] text-[#6B6F7A]">Transfer ke nomor VA BCA</p>
                            </div>
                        </div>
                        <span class="button-text">Pilih</span>
                        <span class="loading-state hidden">
                            <svg class="animate-spin h-5 w-5 text-[#9DAE81]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>

                    <!-- BRI VA -->
                    <button
                        type="button"
                        data-payment-method="virtual_account"
                        data-bank="bri"
                        data-original-text="BRI VA"
                        class="w-full flex items-center justify-between p-4 rounded-lg border-2 border-[#E7E0B8] hover:border-[#9DAE81] hover:bg-[#F5F5FB] transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#003580] rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                BRI
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-[#23252F]">BRI VA</p>
                                <p class="text-[12px] text-[#6B6F7A]">Transfer ke nomor VA BRI</p>
                            </div>
                        </div>
                        <span class="button-text">Pilih</span>
                        <span class="loading-state hidden">
                            <svg class="animate-spin h-5 w-5 text-[#9DAE81]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>

                    <!-- BNI VA -->
                    <button
                        type="button"
                        data-payment-method="virtual_account"
                        data-bank="bni"
                        data-original-text="BNI VA"
                        class="w-full flex items-center justify-between p-4 rounded-lg border-2 border-[#E7E0B8] hover:border-[#9DAE81] hover:bg-[#F5F5FB] transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#0066B2] rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                BNI
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-[#23252F]">BNI VA</p>
                                <p class="text-[12px] text-[#6B6F7A]">Transfer ke nomor VA BNI</p>
                            </div>
                        </div>
                        <span class="button-text">Pilih</span>
                        <span class="loading-state hidden">
                            <svg class="animate-spin h-5 w-5 text-[#9DAE81]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>

                    <!-- Mandiri Bill -->
                    <button
                        type="button"
                        data-payment-method="virtual_account"
                        data-bank="mandiri"
                        data-original-text="Mandiri Bill"
                        class="w-full flex items-center justify-between p-4 rounded-lg border-2 border-[#E7E0B8] hover:border-[#9DAE81] hover:bg-[#F5F5FB] transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#FF6600] rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                MDR
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-[#23252F]">Mandiri Bill</p>
                                <p class="text-[12px] text-[#6B6F7A]">Bayar via Mandiri Bill</p>
                            </div>
                        </div>
                        <span class="button-text">Pilih</span>
                        <span class="loading-state hidden">
                            <svg class="animate-spin h-5 w-5 text-[#9DAE81]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>

                    <!-- Danamon VA -->
                    <button
                        type="button"
                        data-payment-method="virtual_account"
                        data-bank="danamon"
                        data-original-text="Danamon VA"
                        class="w-full flex items-center justify-between p-4 rounded-lg border-2 border-[#E7E0B8] hover:border-[#9DAE81] hover:bg-[#F5F5FB] transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#FF6600] rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                DNM
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-[#23252F]">Danamon VA</p>
                                <p class="text-[12px] text-[#6B6F7A]">Transfer ke nomor VA Danamon</p>
                            </div>
                        </div>
                        <span class="button-text">Pilih</span>
                        <span class="loading-state hidden">
                            <svg class="animate-spin h-5 w-5 text-[#9DAE81]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>

                    <!-- SeaBank VA -->
                    <button
                        type="button"
                        data-payment-method="virtual_account"
                        data-bank="seabank"
                        data-original-text="SeaBank VA"
                        class="w-full flex items-center justify-between p-4 rounded-lg border-2 border-[#E7E0B8] hover:border-[#9DAE81] hover:bg-[#F5F5FB] transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#00A8E8] rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                SEA
                            </div>
                            <div class="text-left">
                                <p class="font-medium text-[#23252F]">SeaBank VA</p>
                                <p class="text-[12px] text-[#6B6F7A]">Transfer ke nomor VA SeaBank</p>
                            </div>
                        </div>
                        <span class="button-text">Pilih</span>
                        <span class="loading-state hidden">
                            <svg class="animate-spin h-5 w-5 text-[#9DAE81]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>

                <!-- Optional: Name input for VA -->
                <div id="va_name_container" class="hidden">
                    <label for="va_name" class="block text-[13px] font-medium text-[#23252F] mb-2">Nama untuk Virtual Account</label>
                    <input
                        type="text"
                        id="va_name"
                        name="va_name"
                        placeholder="Nama Anda"
                        value="{{ Auth::check() ? Auth::user()->name : '' }}"
                        class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                    >
                </div>

                <!-- Anonymous option for logged-in users -->
                @auth
                <div class="flex items-center gap-2 pt-2 border-t border-[#E7E0B8]">
                    <input
                        type="checkbox"
                        id="is_anonymous"
                        name="is_anonymous"
                        class="w-4 h-4 text-[#9DAE81] border-[#E7E0B8] rounded focus:ring-[#9DAE81]"
                    >
                    <label for="is_anonymous" class="text-[13px] text-[#23252F] cursor-pointer">
                        Sembunyikan nama saya (Anonim)
                    </label>
                </div>
                @endauth
            </div>
        </div>

        <!-- QR Code Modal -->
        <div id="qr_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#23252F]">Scan QR Code</h2>
                    <button onclick="document.getElementById('qr_modal').classList.add('hidden')" class="text-[#6B6F7A] hover:text-[#23252F]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="text-center space-y-4">
                    <div class="flex justify-center">
                        <img id="qr_image" src="" alt="QR Code" class="w-64 h-64 border-2 border-[#E7E0B8] rounded-lg">
                    </div>
                    <div class="bg-[#F5F5FB] rounded-lg p-4">
                        <p class="text-[13px] text-[#6B6F7A]">Total Pembayaran</p>
                        <p id="qr_amount" class="text-xl font-bold text-[#23252F]">Rp 0</p>
                    </div>
                    <p class="text-[12px] text-[#6B6F7A]">ID Pembayaran: <span id="qr_payment_id" class="font-mono text-xs"></span></p>
                    <p class="text-[12px] text-[#6B6F7A]">Silakan scan QR code dengan aplikasi e-wallet atau bank Anda</p>
                </div>
            </div>
        </div>

        <!-- Virtual Account Modal -->
        <div id="va_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#23252F]">Virtual Account BCA</h2>
                    <button onclick="document.getElementById('va_modal').classList.add('hidden')" class="text-[#6B6F7A] hover:text-[#23252F]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="text-center space-y-4">
                    <div class="bg-[#F5F5FB] rounded-lg p-6">
                        <p class="text-[13px] text-[#6B6F7A] mb-2">Nomor Virtual Account</p>
                        <p id="va_number_display" class="text-2xl font-bold text-[#23252F] font-mono">-</p>
                    </div>
                    <div class="bg-[#F5F5FB] rounded-lg p-4">
                        <p class="text-[13px] text-[#6B6F7A]">Total Pembayaran</p>
                        <p id="va_amount" class="text-xl font-bold text-[#23252F]">Rp 0</p>
                    </div>
                    <p class="text-[12px] text-[#6B6F7A]">ID Pembayaran: <span id="va_payment_id" class="font-mono text-xs"></span></p>
                    <p id="va_bank_name" class="text-[12px] text-[#6B6F7A]">Silakan transfer sesuai nominal di atas ke nomor Virtual Account</p>
                </div>
            </div>
        </div>
    </body>
</html>
