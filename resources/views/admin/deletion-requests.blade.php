<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Request Penghapusan Kampanye - Admin - EduSorong</title>

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
                    <h1 class="text-[22px] font-semibold">Request Penghapusan Kampanye</h1>
                    <p class="text-[13px] text-[#6B6F7A]">
                        Review dan kelola request penghapusan kampanye dari pengguna.
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
                @if($deletionRequests->isEmpty())
                    <p class="text-[13px] text-[#6B6F7A] text-center py-8">
                        Tidak ada request penghapusan kampanye.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach($deletionRequests as $request)
                            <div class="border border-[#E7E0B8] rounded-xl p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-[15px] font-semibold">{{ $request->campaign->title }}</h3>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($request->status === 'approved') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid md:grid-cols-2 gap-3 text-[12px] mb-3">
                                            <div>
                                                <p class="text-[#6B6F7A]">Pengguna:</p>
                                                <p class="font-medium">{{ $request->user->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[#6B6F7A]">Donasi Terkumpul:</p>
                                                <p class="font-medium text-[13px]">Rp {{ number_format($request->campaign->raised_amount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>

                                        <div class="p-3 bg-gray-50 rounded-lg mb-3">
                                            <p class="text-[12px] text-[#6B6F7A] mb-1">Alasan Penghapusan:</p>
                                            <p class="text-[13px] text-[#23252F]">{{ $request->reason }}</p>
                                        </div>

                                        @if($request->status === 'rejected' && $request->rejection_reason)
                                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-3">
                                                <p class="text-[12px] font-medium text-red-800 mb-1">Alasan Penolakan:</p>
                                                <p class="text-[12px] text-red-700">{{ $request->rejection_reason }}</p>
                                            </div>
                                        @endif

                                        @if($request->reviewed_at)
                                            <p class="text-[11px] text-[#6B6F7A]">
                                                Direview oleh <strong>{{ $request->reviewer->name ?? 'Admin' }}</strong> 
                                                pada {{ $request->reviewed_at->format('d M Y H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if($request->status === 'pending')
                                    <div class="flex gap-2 pt-3 border-t border-[#E7E0B8]">
                                        <form action="{{ route('admin.deletion-requests.approve', $request) }}" method="POST" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                onclick="return confirm('Apakah Anda yakin ingin menyetujui penghapusan kampanye ini? Kampanye akan langsung dihapus.');"
                                                class="px-4 py-2 rounded-full bg-green-600 text-white text-[12px] font-medium hover:bg-green-700 transition-colors"
                                            >
                                                Setujui & Hapus
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.deletion-requests.reject', $request) }}" method="POST" class="inline">
                                            @csrf
                                            <input 
                                                type="text" 
                                                name="rejection_reason" 
                                                placeholder="Alasan penolakan" 
                                                required
                                                class="px-3 py-2 text-[12px] border border-[#E7E0B8] rounded-lg focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                            >
                                            <button type="submit" class="px-4 py-2 rounded-full bg-red-600 text-white text-[12px] font-medium hover:bg-red-700 transition-colors ml-2">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $deletionRequests->links() }}
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>

