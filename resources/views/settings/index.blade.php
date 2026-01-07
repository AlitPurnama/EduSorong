<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Pengaturan - EduSorong</title>

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

        <main class="max-w-4xl mx-auto px-4 lg:px-0 pt-10 pb-16">
            <h1 class="text-[28px] font-semibold mb-6">Pengaturan</h1>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="space-y-6">
                {{-- Profile Section --}}
                <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                    <h2 class="text-[20px] font-semibold mb-4">Profil</h2>

                    <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Photo Upload --}}
                        <div>
                            <label class="block text-[13px] font-medium text-[#23252F] mb-2">Foto Profil</label>
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    @if($user->photo)
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 border-[#E7E0B8]">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-[#2E3242] text-white flex items-center justify-center text-[24px] font-semibold">
                                            {{ strtoupper(mb_substr($user->name ?: $user->email, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <input type="file" name="photo" id="photo" accept="image/*" class="text-[13px]">
                                    <p class="text-[12px] text-[#6B6F7A] mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                                </div>
                            </div>
                            @error('photo')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-[13px] font-medium text-[#23252F] mb-2">Nama Lengkap</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                required
                            >
                            @error('name')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-[13px] font-medium text-[#23252F] mb-2">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                required
                            >
                            @error('email')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-[13px] font-medium text-[#23252F] mb-2">Nomor Handphone</label>
                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                            >
                            @error('phone')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bio --}}
                        <div>
                            <label for="bio" class="block text-[13px] font-medium text-[#23252F] mb-2">Bio</label>
                            <textarea
                                name="bio"
                                id="bio"
                                rows="4"
                                placeholder="Ceritakan tentang dirimu..."
                                class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent resize-none"
                            >{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                        >
                            Simpan Perubahan
                        </button>
                    </form>
                </section>

                {{-- Password Section --}}
                <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                    <h2 class="text-[20px] font-semibold mb-4">Ubah Password</h2>

                    <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-[13px] font-medium text-[#23252F] mb-2">Password Saat Ini</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="current_password"
                                    id="current_password"
                                    class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                    required
                                >
                                <button
                                    type="button"
                                    data-toggle-password="current_password"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B6F7A] hover:text-[#23252F]"
                                >
                                    <x-lucide-eye class="w-5 h-5 hidden" data-icon="eye" />
                                    <x-lucide-eye-off class="w-5 h-5" data-icon="eye-off" />
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-[13px] font-medium text-[#23252F] mb-2">Password Baru</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                    required
                                >
                                <button
                                    type="button"
                                    data-toggle-password="password"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B6F7A] hover:text-[#23252F]"
                                >
                                    <x-lucide-eye class="w-5 h-5 hidden" data-icon="eye" />
                                    <x-lucide-eye-off class="w-5 h-5" data-icon="eye-off" />
                                </button>
                            </div>
                            @error('password')
                                <p class="text-[12px] text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-[13px] font-medium text-[#23252F] mb-2">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="w-full px-4 py-2.5 rounded-lg border border-[#E7E0B8] bg-white text-[14px] focus:outline-none focus:ring-2 focus:ring-[#9DAE81] focus:border-transparent"
                                    required
                                >
                                <button
                                    type="button"
                                    data-toggle-password="password_confirmation"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6B6F7A] hover:text-[#23252F]"
                                >
                                    <x-lucide-eye class="w-5 h-5 hidden" data-icon="eye" />
                                    <x-lucide-eye-off class="w-5 h-5" data-icon="eye-off" />
                                </button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                        >
                            Ubah Password
                        </button>
                    </form>
                </section>

                {{-- KTP Verification Section --}}
                <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                    <h2 class="text-[20px] font-semibold mb-4">Verifikasi KTP</h2>

                    <div class="space-y-4">
                        @php
                            $verificationStatus = $user->ktp_verification_status ?? 'none';
                            $canVerify = in_array($verificationStatus, ['none', 'rejected']);
                        @endphp

                        {{-- Status Display --}}
                        <div class="p-4 rounded-lg
                            @if($verificationStatus === 'pending') bg-yellow-50 border border-yellow-200
                            @elseif($verificationStatus === 'approved') bg-green-50 border border-green-200
                            @elseif($verificationStatus === 'rejected') bg-red-50 border border-red-200
                            @else bg-[#F5F5FB] border border-[#E7E0B8]
                            @endif">
                            <div class="flex items-start gap-3">
                                @if($verificationStatus === 'pending')
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-[14px] font-medium text-yellow-800">Status Verifikasi</p>
                                        <p class="text-[13px] text-yellow-700 mt-1">
                                            <span class="font-medium">Sedang Diproses</span> - Verifikasi KTP Anda sedang dalam proses review oleh admin.
                                        </p>
                                    </div>
                                @elseif($verificationStatus === 'approved')
                                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-[14px] font-medium text-green-800">Status Verifikasi</p>
                                        <p class="text-[13px] text-green-700 mt-1">
                                            <span class="font-medium">✓ Terverifikasi</span> - KTP Anda sudah terverifikasi.
                                        </p>
                                    </div>
                                @elseif($verificationStatus === 'rejected')
                                    <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-[14px] font-medium text-red-800">Status Verifikasi</p>
                                        <p class="text-[13px] text-red-700 mt-1">
                                            <span class="font-medium">Ditolak</span>
                                            @if($user->ktp_rejection_reason)
                                                - {{ $user->ktp_rejection_reason }}
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    <div class="flex-1">
                                        <p class="text-[14px] font-medium text-[#23252F]">Status Verifikasi</p>
                                        <p class="text-[13px] text-[#6B6F7A] mt-1">
                                            Belum terverifikasi
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Button --}}
                        @if($canVerify)
                            <a
                                href="{{ route('settings.ktp.show') }}"
                                class="inline-block px-6 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                            >
                                {{ $verificationStatus === 'rejected' ? 'Ajukan Verifikasi Ulang' : 'Verifikasi KTP' }}
                            </a>
                        @else
                            <p class="text-[12px] text-[#6B6F7A]">
                                @if($verificationStatus === 'pending')
                                    Verifikasi sedang diproses. Anda tidak dapat mengajukan verifikasi baru.
                                @elseif($verificationStatus === 'approved')
                                    Verifikasi sudah disetujui. Anda tidak dapat mengajukan verifikasi baru.
                                @endif
                            </p>
                        @endif
                    </div>
                </section>

                {{-- Organization Verification Section --}}
                <section class="bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] p-6">
                    <h2 class="text-[20px] font-semibold mb-4">Verifikasi Organisasi/Yayasan</h2>

                    <div class="space-y-4">
                        @php
                            $userOrganizations = $user->organizationVerifications()->latest()->get();
                            $pendingCount = $userOrganizations->where('status', 'pending')->count();
                            $approvedCount = $userOrganizations->where('status', 'approved')->count();
                            $canCreate = $pendingCount === 0 && $approvedCount < 3;
                        @endphp

                        @if($approvedCount > 0)
                            <div class="mb-4">
                                <p class="text-[13px] text-[#6B6F7A] mb-2">Organisasi Terverifikasi ({{ $approvedCount }}/3):</p>
                                <div class="space-y-2">
                                    @foreach($userOrganizations->where('status', 'approved') as $org)
                                        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <p class="text-[14px] font-medium text-green-800">
                                                        {{ $org->organization_name }}
                                                        <span class="text-[11px] text-green-600">✓ Terverifikasi</span>
                                                    </p>
                                                    @if($org->address)
                                                        <p class="text-[12px] text-green-700 mt-1">{{ $org->address }}</p>
                                                    @endif
                                                </div>
                                                <a href="{{ route('organization.show', $org) }}" class="text-[12px] text-green-700 hover:underline">
                                                    Detail
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($pendingCount > 0)
                            <div class="mb-4">
                                <p class="text-[13px] text-[#6B6F7A] mb-2">Verifikasi Pending:</p>
                                @foreach($userOrganizations->where('status', 'pending') as $org)
                                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-[14px] font-medium text-yellow-800">
                                                    {{ $org->organization_name }}
                                                    <span class="text-[11px] text-yellow-600">⏳ Menunggu</span>
                                                </p>
                                            </div>
                                            <a href="{{ route('organization.show', $org) }}" class="text-[12px] text-yellow-700 hover:underline">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($canCreate)
                            <a
                                href="{{ route('organization.create') }}"
                                class="inline-block px-6 py-2.5 rounded-full bg-[#9DAE81] text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-[#8FA171] transition-colors"
                            >
                                + Verifikasi Organisasi Baru
                            </a>
                        @else
                            @if(!$ktpVerified)
                                <a
                                    href="{{ route('settings.ktp.show') }}"
                                    class="inline-block px-6 py-2.5 rounded-full bg-yellow-500 text-white font-semibold shadow-[0_8px_18px_rgba(0,0,0,0.16)] hover:bg-yellow-600 transition-colors"
                                >
                                    Verifikasi KTP Terlebih Dahulu
                                </a>
                            @elseif($pendingCount > 0)
                                <p class="text-[12px] text-[#6B6F7A]">
                                    Anda memiliki verifikasi yang sedang diproses. Tunggu hingga selesai sebelum mengajukan yang baru.
                                </p>
                            @elseif($approvedCount >= 3)
                                <p class="text-[12px] text-[#6B6F7A]">
                                    Anda sudah mencapai batas maksimal 3 organisasi terverifikasi.
                                </p>
                            @endif
                        @endif

                        @if($userOrganizations->where('status', 'rejected')->count() > 0)
                            <div class="mt-4">
                                <p class="text-[13px] text-[#6B6F7A] mb-2">Verifikasi Ditolak:</p>
                                @foreach($userOrganizations->where('status', 'rejected') as $org)
                                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg mb-2">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-[14px] font-medium text-red-800">
                                                    {{ $org->organization_name }}
                                                    <span class="text-[11px] text-red-600">✗ Ditolak</span>
                                                </p>
                                                @if($org->rejection_reason)
                                                    <p class="text-[12px] text-red-700 mt-1">
                                                        <strong>Alasan:</strong> {{ $org->rejection_reason }}
                                                    </p>
                                                @endif
                                            </div>
                                            <a href="{{ route('organization.show', $org) }}" class="text-[12px] text-red-700 hover:underline">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>

