<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Upload Bukti Penggunaan - EduSorong</title>

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
                <a href="{{ route('withdrawal.show', $withdrawal) }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-2 inline-block">
                    ← Kembali ke Detail Pencairan
                </a>
                <h1 class="text-[22px] font-semibold">Upload Bukti Penggunaan Dana</h1>
            </div>

            @if (session('error'))
                <div class="px-4 py-2 rounded-md bg-[#FEF2F2] text-[13px] text-[#991B1B] border border-[#FECACA]">
                    {{ session('error') }}
                </div>
            @endif

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-[12px] text-blue-800">
                        <strong>Info:</strong> Upload bukti penggunaan dana yang telah dicairkan. Format: Foto atau PDF (maksimal 5MB).
                        Bukti akan diverifikasi oleh admin sebelum ditampilkan di laporan transparansi.
                    </p>
                </div>

                <form action="{{ route('withdrawal.evidence.store', $withdrawal) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="description" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Deskripsi Penggunaan Dana (Opsional)
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Jelaskan penggunaan dana..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="evidence" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Bukti Penggunaan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="file"
                            name="evidence"
                            id="evidence"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                            class="block w-full text-[12px] text-[#6B6F7A] file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-[#9DAE81] file:text-white hover:file:bg-[#8FA171]"
                        />
                        <p class="text-[12px] text-[#6B6F7A] mt-1">Format: PDF, JPG, PNG. Maksimal 5MB.</p>
                        @error('evidence')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="used_at" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Tanggal Penggunaan (Opsional)
                        </label>
                        <input
                            type="date"
                            name="used_at"
                            id="used_at"
                            value="{{ old('used_at') }}"
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                        />
                        @error('used_at')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            class="px-6 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-semibold hover:bg-[#8FA171] transition-colors"
                        >
                            Upload Bukti
                        </button>
                        <a
                            href="{{ route('withdrawal.show', $withdrawal) }}"
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

