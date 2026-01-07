<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Request Pencairan Dana - EduSorong</title>

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
                <a href="{{ route('campaigns.show', $campaign) }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-2 inline-block">
                    ← Kembali ke Kampanye
                </a>
                <h1 class="text-[22px] font-semibold">Request Pencairan Dana</h1>
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
                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-[12px] text-blue-800">
                        <strong>Info:</strong> Anda dapat request pencairan dana jika kampanye sudah mencapai minimal 80% dari target.
                        Dana tersedia: <strong>Rp {{ number_format($campaign->raised_amount, 0, ',', '.') }}</strong>
                    </p>
                </div>

                <form action="{{ route('withdrawal.store', $campaign) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-[13px] font-medium text-[#23252F] mb-1">
                            Jumlah yang Dicairkan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="requested_amount"
                            value="{{ old('requested_amount') }}"
                            min="10000"
                            max="{{ $campaign->raised_amount }}"
                            step="1000"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Minimal Rp 10.000"
                        />
                        @error('requested_amount')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[11px] text-[#6B6F7A] mt-1">
                            Maksimal: Rp {{ number_format($campaign->raised_amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-[#23252F] mb-1">
                            Tujuan Penggunaan Dana <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="purpose"
                            rows="4"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Jelaskan untuk apa dana ini akan digunakan..."
                        >{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[13px] font-medium text-[#23252F] mb-1">
                                Nama Bank <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="bank_name"
                                value="{{ old('bank_name') }}"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                placeholder="Contoh: BCA, BRI, Mandiri"
                            />
                            @error('bank_name')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[13px] font-medium text-[#23252F] mb-1">
                                Nomor Rekening <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="bank_account_number"
                                value="{{ old('bank_account_number') }}"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                placeholder="Nomor rekening"
                            />
                            @error('bank_account_number')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-[#23252F] mb-1">
                            Nama Pemilik Rekening <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="bank_account_name"
                            value="{{ old('bank_account_name') }}"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Nama sesuai rekening"
                        />
                        @error('bank_account_name')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            class="px-6 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-semibold hover:bg-[#8FA171] transition-colors"
                        >
                            Kirim Request
                        </button>
                        <a
                            href="{{ route('campaigns.show', $campaign) }}"
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

