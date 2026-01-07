<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Request Pencairan Dana - Admin - EduSorong</title>

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
                    <a href="{{ route('admin.dashboard') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-2 inline-block">
                        ← Kembali ke Dashboard
                    </a>
                    <h1 class="text-[22px] font-semibold">Request Pencairan Dana</h1>
                    <p class="text-[13px] text-[#6B6F7A]">
                        Review dan kelola request pencairan dana dari kampanye.
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

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                @if($withdrawals->isEmpty())
                    <p class="text-[13px] text-[#6B6F7A] text-center py-8">
                        Tidak ada request pencairan dana.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach($withdrawals as $withdrawal)
                            <div class="border border-[#E7E0B8] rounded-xl p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-[15px] font-semibold">{{ $withdrawal->campaign->title }}</h3>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                                @if($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-800
                                                @elseif($withdrawal->status === 'completed') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($withdrawal->status) }}
                                            </span>
                                        </div>
                                        <div class="grid md:grid-cols-2 gap-3 text-[12px]">
                                            <div>
                                                <p class="text-[#6B6F7A]">Pengguna:</p>
                                                <p class="font-medium">{{ $withdrawal->user->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[#6B6F7A]">Jumlah Request:</p>
                                                <p class="font-medium text-[13px]">{{ $withdrawal->formatted_amount }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[#6B6F7A]">Dana Tersedia:</p>
                                                <p class="font-medium text-[13px]">Rp {{ number_format($withdrawal->campaign->raised_amount, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[#6B6F7A]">Progress Kampanye:</p>
                                                <p class="font-medium text-[13px]">{{ number_format($withdrawal->campaign->progress_percentage, 1) }}%</p>
                                            </div>
                                        </div>
                                        @if($withdrawal->purpose)
                                            <div class="mt-2">
                                                <p class="text-[#6B6F7A] text-[12px]">Tujuan Penggunaan:</p>
                                                <p class="text-[13px]">{{ $withdrawal->purpose }}</p>
                                            </div>
                                        @endif
                                        <div class="mt-2 p-2 bg-gray-50 rounded text-[12px]">
                                            <p class="text-[#6B6F7A]">Rekening Bank:</p>
                                            <p class="font-medium">{{ $withdrawal->bank_name }}</p>
                                            <p class="font-medium">{{ $withdrawal->bank_account_name }}</p>
                                            <p class="font-medium">{{ $withdrawal->bank_account_number }}</p>
                                        </div>
                                        @if($withdrawal->status === 'rejected' && $withdrawal->rejection_reason)
                                            <div class="mt-2 p-2 bg-red-50 rounded text-[12px] text-red-800">
                                                <strong>Alasan ditolak:</strong> {{ $withdrawal->rejection_reason }}
                                            </div>
                                        @endif
                                        @if($withdrawal->reviewed_at)
                                            <p class="text-[11px] text-[#6B6F7A] mt-2">
                                                Direview oleh {{ $withdrawal->reviewer->name ?? 'Admin' }} 
                                                pada {{ $withdrawal->reviewed_at->format('d M Y H:i') }}
                                            </p>
                                        @endif
                                        @if($withdrawal->completed_at)
                                            <p class="text-[11px] text-[#6B6F7A] mt-2">
                                                Diselesaikan pada {{ $withdrawal->completed_at->format('d M Y H:i') }}
                                            </p>
                                        @endif

                                        {{-- Evidence Section --}}
                                        @if($withdrawal->evidences->count() > 0)
                                            <div class="mt-3 pt-3 border-t border-[#E7E0B8]">
                                                <p class="text-[12px] font-medium text-[#23252F] mb-2">Bukti Penggunaan:</p>
                                                <div class="space-y-2">
                                                    @foreach($withdrawal->evidences as $evidence)
                                                        <div class="p-3 bg-gray-50 rounded-lg">
                                                            <div class="flex items-start justify-between mb-2">
                                                                <div class="flex-1">
                                                                    @if($evidence->description)
                                                                        <p class="text-[12px] text-[#23252F] mb-1">{{ $evidence->description }}</p>
                                                                    @endif
                                                                    <div class="flex items-center gap-2 mb-2">
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
                                                                    @if($evidence->rejection_reason)
                                                                        <p class="text-[11px] text-red-600">
                                                                            <strong>Alasan ditolak:</strong> {{ $evidence->rejection_reason }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                                <div class="flex gap-2 ml-3">
                                                                    <a href="{{ $evidence->evidence_url }}" target="_blank" class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-[11px] font-medium hover:bg-blue-200">
                                                                        Lihat
                                                                    </a>
                                                                    @if($evidence->status === 'pending')
                                                                        <form action="{{ route('admin.evidences.verify', $evidence) }}" method="POST" class="inline">
                                                                            @csrf
                                                                            <input type="hidden" name="action" value="approve">
                                                                            <button type="submit" class="px-2 py-1 rounded bg-green-100 text-green-700 text-[11px] font-medium hover:bg-green-200">
                                                                                ✓ Setujui
                                                                            </button>
                                                                        </form>
                                                                        <button onclick="document.getElementById('reject_evidence_{{ $evidence->id }}').classList.toggle('hidden')" class="px-2 py-1 rounded bg-red-100 text-red-700 text-[11px] font-medium hover:bg-red-200">
                                                                            ✗ Tolak
                                                                        </button>
                                                                        <form id="reject_evidence_{{ $evidence->id }}" action="{{ route('admin.evidences.verify', $evidence) }}" method="POST" class="hidden inline-block ml-2">
                                                                            @csrf
                                                                            <input type="hidden" name="action" value="reject">
                                                                            <input type="text" name="rejection_reason" placeholder="Alasan" required class="px-2 py-1 text-[11px] border rounded">
                                                                            <button type="submit" class="px-2 py-1 rounded bg-red-600 text-white text-[11px] font-medium">
                                                                                Submit
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($withdrawal->status === 'pending')
                                    <div class="flex gap-2 pt-2 border-t border-[#E7E0B8]">
                                        <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-full bg-green-600 text-white text-[12px] font-medium hover:bg-green-700 transition-colors">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="text" name="rejection_reason" placeholder="Alasan penolakan" required
                                                class="px-3 py-2 text-[12px] border border-[#E7E0B8] rounded-lg focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent">
                                            <button type="submit" class="px-4 py-2 rounded-full bg-red-600 text-white text-[12px] font-medium hover:bg-red-700 transition-colors ml-2">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @elseif($withdrawal->status === 'approved')
                                    <div class="flex gap-2 pt-2 border-t border-[#E7E0B8]">
                                        <form action="{{ route('admin.withdrawals.complete', $withdrawal) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-full bg-blue-600 text-white text-[12px] font-medium hover:bg-blue-700 transition-colors">
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $withdrawals->links() }}
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>

