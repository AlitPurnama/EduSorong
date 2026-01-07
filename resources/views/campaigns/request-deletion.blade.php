<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Request Hapus Kampanye - EduSorong</title>

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
                <a href="{{ route('dashboard') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-2 inline-block">
                    ← Kembali ke Dashboard
                </a>
                <h1 class="text-[22px] font-semibold">Request Hapus Kampanye</h1>
                <p class="text-[13px] text-[#6B6F7A]">
                    Kampanye: <strong>{{ $campaign->title }}</strong>
                </p>
            </div>

            @if (session('error'))
                <div class="px-4 py-2 rounded-md bg-[#FEF2F2] text-[13px] text-[#991B1B] border border-[#FECACA]">
                    {{ session('error') }}
                </div>
            @endif

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <p class="text-[13px] font-medium text-yellow-800 mb-1">Perhatian!</p>
                            <p class="text-[12px] text-yellow-700">
                                Kampanye ini sudah memiliki donasi sebesar <strong>Rp {{ number_format($campaign->raised_amount, 0, ',', '.') }}</strong>. 
                                Penghapusan kampanye memerlukan persetujuan admin. Silakan berikan alasan yang jelas mengapa kampanye ini perlu dihapus.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('dashboard.campaigns.destroy', $campaign) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label for="reason" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Alasan Penghapusan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="reason"
                            id="reason"
                            rows="6"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Jelaskan alasan mengapa kampanye ini perlu dihapus..."
                        >{{ old('reason') }}</textarea>
                        <p class="text-[12px] text-[#6B6F7A] mt-1">Alasan ini akan ditinjau oleh admin sebelum kampanye dihapus.</p>
                        @error('reason')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            class="px-6 py-2 rounded-full bg-red-600 text-white text-[13px] font-semibold hover:bg-red-700 transition-colors"
                        >
                            Kirim Request Penghapusan
                        </button>
                        <a
                            href="{{ route('dashboard') }}"
                            class="px-6 py-2 rounded-full bg-white border border-[#E7E0B8] text-[#23252F] text-[13px] font-semibold hover:bg-[#F9F3DB] transition-colors"
                        >
                            Batal
                        </a>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>

