<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Verifikasi Organisasi - EduSorong</title>

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
                <a href="{{ route('organization.index') }}" class="text-[13px] text-[#6B6F7A] hover:text-[#23252F] mb-2 inline-block">
                    ← Kembali ke Daftar Organisasi
                </a>
                <h1 class="text-[22px] font-semibold">Verifikasi Organisasi/Yayasan Baru</h1>
                <p class="text-[13px] text-[#6B6F7A]">
                    Lengkapi data organisasi/yayasan Anda untuk verifikasi.
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
                        <strong>Catatan:</strong> Anda dapat memiliki maksimal 3 organisasi terverifikasi. 
                        Pastikan dokumen yang diupload jelas dan valid (AKTA, SIUP, atau dokumen legal lainnya).
                    </p>
                </div>

                <form action="{{ route('organization.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="organization_name" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Nama Organisasi/Yayasan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="organization_name"
                            id="organization_name"
                            value="{{ old('organization_name') }}"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Nama lengkap organisasi/yayasan"
                        />
                        @error('organization_name')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="organization_description" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Deskripsi Organisasi (Opsional)
                        </label>
                        <textarea
                            name="organization_description"
                            id="organization_description"
                            rows="4"
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Ceritakan tentang organisasi/yayasan Anda..."
                        >{{ old('organization_description') }}</textarea>
                        @error('organization_description')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="npwp" class="block text-[13px] font-medium text-[#23252F] mb-2">
                                NPWP <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="npwp"
                                id="npwp"
                                value="{{ old('npwp') }}"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                placeholder="12.345.678.9-012.345"
                            />
                            @error('npwp')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-[13px] font-medium text-[#23252F] mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old('phone') }}"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                placeholder="081234567890"
                            />
                            @error('phone')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="website" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Website (Opsional)
                        </label>
                        <input
                            type="url"
                            name="website"
                            id="website"
                            value="{{ old('website') }}"
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="https://example.com"
                        />
                        @error('website')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="address" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Alamat (Opsional)
                        </label>
                        <textarea
                            name="address"
                            id="address"
                            rows="3"
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            placeholder="Alamat lengkap organisasi/yayasan"
                        >{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="document" class="block text-[13px] font-medium text-[#23252F] mb-2">
                            Dokumen Verifikasi <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="file"
                            name="document"
                            id="document"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required
                            class="w-full px-4 py-2 rounded-lg border border-[#E7E0B8] text-[13px] focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                        />
                        <p class="text-[12px] text-[#6B6F7A] mt-1">Format: PDF, JPG, PNG. Maksimal 5MB. Upload dokumen legal (AKTA, SIUP, dll).</p>
                        @error('document')
                            <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="submit"
                            class="px-6 py-2 rounded-full bg-[#9DAE81] text-white text-[13px] font-semibold hover:bg-[#8FA171] transition-colors"
                        >
                            Kirim untuk Verifikasi
                        </button>
                        <a
                            href="{{ route('organization.index') }}"
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

