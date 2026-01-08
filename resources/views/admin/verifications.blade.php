<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Verifikasi Yayasan - Admin - EduSorong</title>

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
                    <h1 class="text-[22px] font-semibold">Verifikasi Yayasan</h1>
                    <p class="text-[13px] text-[#6B6F7A]">
                        Review dan verifikasi yayasan yang mendaftar.
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
                @if($verifications->isEmpty())
                    <p class="text-[13px] text-[#6B6F7A] text-center py-8">
                        Tidak ada verifikasi yayasan yang perlu direview.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach($verifications as $verification)
                            <div class="border border-[#E7E0B8] rounded-xl p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-[15px] font-semibold">{{ $verification->organization_name }}</h3>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                                @if($verification->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($verification->status === 'approved') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($verification->status) }}
                                            </span>
                                        </div>
                                        <p class="text-[12px] text-[#6B6F7A] mb-1">
                                            Pengguna: <span class="font-medium">{{ $verification->user->name }}</span> ({{ $verification->user->email }})
                                        </p>
                                        @if($verification->organization_description)
                                            <p class="text-[13px] text-[#23252F] mt-2">{{ $verification->organization_description }}</p>
                                        @endif
                                        <div class="grid md:grid-cols-2 gap-3 text-[12px] mt-2">
                                            @if($verification->npwp)
                                                <div>
                                                    <p class="text-[#6B6F7A]">NPWP:</p>
                                                    <p class="font-medium">{{ $verification->npwp }}</p>
                                                </div>
                                            @endif
                                            @if($verification->phone)
                                                <div>
                                                    <p class="text-[#6B6F7A]">Telepon:</p>
                                                    <p class="font-medium">{{ $verification->phone }}</p>
                                                </div>
                                            @endif
                                            @if($verification->website)
                                                <div>
                                                    <p class="text-[#6B6F7A]">Website:</p>
                                                    <a href="{{ $verification->website }}" target="_blank" class="font-medium text-[#3B82F6] hover:underline">{{ $verification->website }}</a>
                                                </div>
                                            @endif
                                            @if($verification->address)
                                                <div>
                                                    <p class="text-[#6B6F7A]">Alamat:</p>
                                                    <p class="font-medium">{{ $verification->address }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        @if($verification->document_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $verification->document_path) }}" target="_blank" class="text-[12px] text-[#3B82F6] hover:underline inline-block">
                                                    Lihat Dokumen Verifikasi →
                                                </a>
                                            </div>
                                        @endif
                                        @if($verification->status === 'rejected' && $verification->rejection_reason)
                                            <div class="mt-2 p-2 bg-red-50 rounded text-[12px] text-red-800">
                                                <strong>Alasan ditolak:</strong> {{ $verification->rejection_reason }}
                                            </div>
                                        @endif
                                        @if($verification->verified_at)
                                            <p class="text-[11px] text-[#6B6F7A] mt-2">
                                                Diverifikasi oleh {{ $verification->verifier->name ?? 'Admin' }}
                                                pada <span data-utc-time="{{ $verification->verified_at->toIso8601String() }}" data-format="date-time">{{ $verification->verified_at->format('d M Y H:i') }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if($verification->status === 'pending')
                                    <div class="flex gap-2 pt-2 border-t border-[#E7E0B8]">
                                        <form action="{{ route('admin.verifications.approve', $verification) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-full bg-green-600 text-white text-[12px] font-medium hover:bg-green-700 transition-colors">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.verifications.reject', $verification) }}" method="POST" class="inline" id="reject-form-{{ $verification->id }}">
                                            @csrf
                                            <input type="text" name="rejection_reason" placeholder="Alasan penolakan" required
                                                class="px-3 py-2 text-[12px] border border-[#E7E0B8] rounded-lg focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent">
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
                        {{ $verifications->links() }}
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>

