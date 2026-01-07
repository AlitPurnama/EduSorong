<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Detail Request Pencairan - EduSorong</title>

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

        <main class="max-w-3xl mx-auto px-4 lg:px-4 pt-10 pb-16 space-y-8">
            <div>
                <a href="{{ route('campaigns.show', $withdrawal->campaign) }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-2 inline-block">
                    ← Kembali ke Kampanye
                </a>
                <h1 class="text-[22px] font-semibold">Detail Request Pencairan Dana</h1>
                <p class="text-[13px] text-[#6B6F7A]">
                    Kampanye: <strong>{{ $withdrawal->campaign->title }}</strong>
                </p>
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

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6 space-y-4">
                <div class="flex items-center gap-2 pb-4 border-b border-[#E7E0B8]">
                    <h3 class="text-[16px] font-semibold text-[#23252F]">Status Request</h3>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                        @if($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($withdrawal->status === 'completed') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($withdrawal->status) }}
                    </span>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[12px] text-[#6B6F7A] mb-1">Jumlah Request</p>
                        <p class="text-[16px] font-semibold text-[#23252F]">{{ $withdrawal->formatted_amount }}</p>
                    </div>

                    <div>
                        <p class="text-[12px] text-[#6B6F7A] mb-1">Dana Tersedia</p>
                        <p class="text-[16px] font-semibold text-[#23252F]">
                            Rp {{ number_format($withdrawal->campaign->raised_amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[12px] text-[#6B6F7A] mb-1">Progress Kampanye</p>
                        <p class="text-[16px] font-semibold text-[#23252F]">
                            {{ number_format($withdrawal->campaign->progress_percentage, 1) }}%
                        </p>
                    </div>

                    <div>
                        <p class="text-[12px] text-[#6B6F7A] mb-1">Tanggal Request</p>
                        <p class="text-[14px] font-medium text-[#23252F]">
                            {{ $withdrawal->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>

                @if($withdrawal->purpose)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <p class="text-[13px] font-medium text-[#23252F] mb-2">Tujuan Penggunaan Dana</p>
                        <p class="text-[13px] text-[#50545F] leading-relaxed">{{ $withdrawal->purpose }}</p>
                    </div>
                @endif

                <div class="pt-4 border-t border-[#E7E0B8]">
                    <p class="text-[13px] font-medium text-[#23252F] mb-3">Informasi Rekening Bank</p>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-[13px]">
                        <div>
                            <p class="text-[#6B6F7A]">Nama Bank</p>
                            <p class="font-medium text-[#23252F]">{{ $withdrawal->bank_name }}</p>
                        </div>
                        <div>
                            <p class="text-[#6B6F7A]">Nama Pemilik Rekening</p>
                            <p class="font-medium text-[#23252F]">{{ $withdrawal->bank_account_name }}</p>
                        </div>
                        <div>
                            <p class="text-[#6B6F7A]">Nomor Rekening</p>
                            <p class="font-medium text-[#23252F]">{{ $withdrawal->bank_account_number }}</p>
                        </div>
                    </div>
                </div>

                @if($withdrawal->status === 'rejected' && $withdrawal->rejection_reason)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-[13px] font-medium text-red-800 mb-1">Alasan Penolakan</p>
                            <p class="text-[13px] text-red-700">{{ $withdrawal->rejection_reason }}</p>
                        </div>
                    </div>
                @endif

                @if($withdrawal->reviewed_at)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <p class="text-[12px] text-[#6B6F7A]">
                            Direview oleh <strong>{{ $withdrawal->reviewer->name ?? 'Admin' }}</strong> 
                            pada {{ $withdrawal->reviewed_at->format('d M Y H:i') }}
                        </p>
                    </div>
                @endif

                @if($withdrawal->completed_at)
                    <div class="pt-4 border-t border-[#E7E0B8]">
                        <p class="text-[12px] text-[#6B6F7A]">
                            Diselesaikan pada {{ $withdrawal->completed_at->format('d M Y H:i') }}
                        </p>
                    </div>
                @endif
            </section>

            {{-- Evidence Section --}}
            @if($withdrawal->status === 'completed')
                <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-[20px] font-semibold">Bukti Penggunaan Dana</h2>
                        <a
                            href="{{ route('withdrawal.evidence.create', $withdrawal) }}"
                            class="px-4 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-semibold hover:bg-[#8FA171] transition-colors"
                        >
                            + Upload Bukti
                        </a>
                    </div>

                    @if($withdrawal->evidences->count() > 0)
                        <div class="space-y-4">
                            @foreach($withdrawal->evidences as $evidence)
                                <div class="p-4 border border-[#E7E0B8] rounded-lg">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            @if($evidence->description)
                                                <p class="text-[13px] text-[#23252F] mb-2">{{ $evidence->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                                    @if($evidence->status === 'verified') bg-green-100 text-green-800
                                                    @elseif($evidence->status === 'rejected') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800
                                                    @endif">
                                                    {{ ucfirst($evidence->status) }}
                                                </span>
                                                @if($evidence->used_at)
                                                    <span class="text-[12px] text-[#6B6F7A]">
                                                        Digunakan: {{ $evidence->used_at->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($evidence->rejection_reason)
                                                <p class="text-[12px] text-red-600 mt-2">
                                                    <strong>Alasan ditolak:</strong> {{ $evidence->rejection_reason }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2 ml-4">
                                            <a
                                                href="{{ $evidence->evidence_url }}"
                                                target="_blank"
                                                class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-[12px] font-medium hover:bg-gray-200 transition-colors"
                                            >
                                                Lihat
                                            </a>
                                            @if(!$evidence->isVerified())
                                                <form
                                                    action="{{ route('withdrawal.evidence.destroy', [$withdrawal, $evidence]) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus bukti ini?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 text-[12px] font-medium hover:bg-red-200 transition-colors"
                                                    >
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-[13px] text-[#6B6F7A]">
                            <p>Belum ada bukti penggunaan yang diupload.</p>
                            <p class="mt-1">Silakan upload bukti penggunaan dana yang telah dicairkan.</p>
                        </div>
                    @endif
                </section>
            @endif
        </main>
    </body>
</html>

