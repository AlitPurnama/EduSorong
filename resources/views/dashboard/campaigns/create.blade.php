<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Buat Kampanye - EduSorong</title>

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

        <main class="max-w-4xl mx-auto px-4 lg:px-4 pt-10 pb-16 space-y-8">
            <a href="{{ route('dashboard') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F]">
                &larr; Kembali ke dashboard
            </a>

            <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                <h1 class="text-[20px] font-semibold mb-4">Buat Kampanye Baru</h1>

                <form
                    action="{{ route('dashboard.campaigns.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-4"
                >
                    @csrf

                    <div class="space-y-1 text-[13px]">
                        <label for="title" class="font-medium text-[#2E3242]">Judul Kampanye</label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            required
                            class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                        />
                        @error('title')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-1 text-[13px]">
                            <label for="location" class="font-medium text-[#2E3242]">Lokasi</label>
                            <input
                                id="location"
                                name="location"
                                type="text"
                                value="{{ old('location') }}"
                                class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                            />
                        </div>
                        <div class="space-y-1 text-[13px]">
                            <label for="organization_verification_id" class="font-medium text-[#2E3242]">Organisasi/Yayasan</label>
                            <select
                                id="organization_verification_id"
                                name="organization_verification_id"
                                class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                            >
                                <option value="">Pilih Organisasi (Opsional - Perorangan)</option>
                                @foreach($approvedOrganizations ?? [] as $org)
                                    <option value="{{ $org->id }}" {{ old('organization_verification_id') == $org->id ? 'selected' : '' }}>
                                        {{ $org->organization_name }} ✓ Terverifikasi
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-[#8C8F99] mt-1">
                                Pilih organisasi yang sudah terverifikasi. Jika tidak dipilih, kampanye akan dikelola sebagai perorangan.
                            </p>
                            @if(($approvedOrganizations ?? collect())->isEmpty())
                                <p class="text-[11px] text-[#F59E0B] mt-1">
                                    Belum ada organisasi terverifikasi. <a href="{{ route('organization.create') }}" class="underline">Verifikasi organisasi</a> untuk menampilkan badge terverifikasi.
                                </p>
                            @endif
                            @error('organization_verification_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-1 text-[13px]">
                            <label for="image" class="font-medium text-[#2E3242]">Gambar Kampanye</label>
                            <input
                                id="image"
                                name="image"
                                type="file"
                                accept="image/*"
                                class="block w-full text-[12px] text-[#6B6F7A] file:mr-4 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-[#9DAE81] file:text-white hover:file:bg-[#8FA171]"
                            />
                            <p class="text-[11px] text-[#8C8F99]">
                                Format: JPG, PNG, atau WEBP. Maksimal 2MB.
                            </p>
                            @error('image')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1 text-[13px]">
                        <label for="target_amount" class="font-medium text-[#2E3242]">Target Donasi (Rp)</label>
                        <input
                            id="target_amount"
                            name="target_amount"
                            type="number"
                            min="0"
                            value="{{ old('target_amount') }}"
                            required
                            class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                        />
                        @error('target_amount')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1 text-[13px]">
                        <label for="excerpt" class="font-medium text-[#2E3242]">Ringkasan Singkat</label>
                        <input
                            id="excerpt"
                            name="excerpt"
                            type="text"
                            value="{{ old('excerpt') }}"
                            class="h-10 w-full rounded-[10px] border border-[#E0E3F0] px-3 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                        />
                    </div>

                    <div class="space-y-1 text-[13px]">
                        <label for="description" class="font-medium text-[#2E3242]">Deskripsi Lengkap</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="w-full rounded-[10px] border border-[#E0E3F0] px-3 py-2 text-[13px] outline-none focus:ring-2 focus:ring-[#9DAE81]"
                        >{{ old('description') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="mt-4 h-10 w-full rounded-[10px] bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                    >
                        Simpan Kampanye
                    </button>
                </form>
            </section>
        </main>
    </body>
</html>


