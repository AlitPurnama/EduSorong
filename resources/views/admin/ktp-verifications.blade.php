<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Verifikasi KTP - Admin - EduSorong</title>

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
                    <h1 class="text-[22px] font-semibold">Verifikasi KTP</h1>
                    <p class="text-[13px] text-[#6B6F7A]">
                        Review dan verifikasi KTP pengguna.
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
                @if($users->isEmpty())
                    <p class="text-[13px] text-[#6B6F7A] text-center py-8">
                        Tidak ada verifikasi KTP yang perlu direview.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach($users as $user)
                            <div class="border border-[#E7E0B8] rounded-xl p-4 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="text-[15px] font-semibold">{{ $user->name }}</h3>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                                @if($user->ktp_verification_status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($user->ktp_verification_status === 'approved') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($user->ktp_verification_status) }}
                                            </span>
                                        </div>
                                        <div class="grid md:grid-cols-2 gap-3 text-[12px]">
                                            <div>
                                                <p class="text-[#6B6F7A]">Email:</p>
                                                <p class="font-medium">{{ $user->email }}</p>
                                            </div>
                                            @if($user->ktp_number)
                                                <div>
                                                    <p class="text-[#6B6F7A]">Nomor KTP:</p>
                                                    <p class="font-medium">{{ $user->ktp_number }}</p>
                                                </div>
                                            @endif
                                            @if($user->ktp_name)
                                                <div>
                                                    <p class="text-[#6B6F7A]">Nama Sesuai KTP:</p>
                                                    <p class="font-medium">{{ $user->ktp_name }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        @if($user->ktp_photo)
                                            <div class="mt-3">
                                                <p class="text-[#6B6F7A] text-[12px] mb-2">Foto KTP:</p>
                                                <a href="{{ asset('storage/' . $user->ktp_photo) }}" target="_blank" class="inline-block">
                                                    <img src="{{ asset('storage/' . $user->ktp_photo) }}" alt="Foto KTP" class="max-w-xs rounded-lg border border-[#E7E0B8] hover:border-[#9DAE81] transition-colors">
                                                </a>
                                            </div>
                                        @endif
                                        @if($user->ktp_verification_status === 'rejected' && $user->ktp_rejection_reason)
                                            <div class="mt-2 p-2 bg-red-50 rounded text-[12px] text-red-800">
                                                <strong>Alasan ditolak:</strong> {{ $user->ktp_rejection_reason }}
                                            </div>
                                        @endif
                                        @if($user->updated_at && $user->ktp_verification_status !== 'none')
                                            <p class="text-[11px] text-[#6B6F7A] mt-2">
                                                Terakhir diupdate: <span data-utc-time="{{ $user->updated_at->toIso8601String() }}" data-format="date-time">{{ $user->updated_at->format('d M Y H:i') }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if($user->ktp_verification_status === 'pending')
                                    <div class="flex gap-2 pt-2 border-t border-[#E7E0B8]">
                                        <form action="{{ route('admin.ktp-verifications.approve', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 rounded-full bg-green-600 text-white text-[12px] font-medium hover:bg-green-700 transition-colors">
                                                Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.ktp-verifications.reject', $user) }}" method="POST" class="inline">
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
                        {{ $users->links() }}
                    </div>
                @endif
            </section>
        </main>
    </body>
</html>

