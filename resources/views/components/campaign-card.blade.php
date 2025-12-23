@props([
    'location' => 'Sorong Utara',
    'title' => 'Bantu Marcel Masuk SMA',
    'raised' => 'Rp 3.750.000',
    'target' => 'Rp 5.000.000',
    'organization' => 'Nama Yayasan',
    'image' => null,
    'href' => null,
])

@php
    $image = $image ?? asset('images/placeholder.jpg');

    $rawRaised = $raised;
    $rawTarget = $target;

    $progress = null;
    if (is_numeric($rawRaised) && is_numeric($rawTarget) && $rawTarget > 0) {
        $progress = max(0, min(100, ($rawRaised / $rawTarget) * 100));
        $raised = 'Rp ' . number_format($rawRaised, 0, ',', '.');
        $target = 'Rp ' . number_format($rawTarget, 0, ',', '.');
    } elseif (is_numeric($rawTarget) && $rawTarget > 0) {
        $progress = 0;
        $raised = 'Rp ' . number_format((int) $rawRaised, 0, ',', '.');
        $target = 'Rp ' . number_format($rawTarget, 0, ',', '.');
    } else {
        // Placeholder mode
        $progress = 66;
    }
@endphp

<a
    @if ($href)
        href="{{ $href }}"
    @endif
    class="block bg-white rounded-2xl border border-[#E7E0B8] shadow-[0_8px_20px_rgba(0,0,0,0.08)] overflow-hidden text-[12px] hover:shadow-[0_12px_26px_rgba(0,0,0,0.12)] hover:-translate-y-0.5 transition-transform transition-shadow"
>
    {{-- Image header --}}
    <div class="relative bg-[#F4F5FA]">
        <img
            src="{{ $image }}"
            alt="Gambar kampanye"
            class="w-full h-[140px] object-cover"
        />
        <div class="absolute inset-x-0 top-0 px-3 pt-3 flex justify-between items-start pointer-events-none">
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-white/90 text-[11px] text-[#50545F] border border-[#E0E3F0] shadow-xs"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-[#9DAE81]"></span>
                <span class="truncate max-w-[140px]">{{ $location }}</span>
            </span>
            <div
                class="w-6 h-6 rounded-full border border-[#D5D8E2] bg-white/90 flex items-center justify-center text-[10px] text-[#868A95]"
            >
                <x-lucide-settings-2 class="w-3.5 h-3.5" />
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="p-4 space-y-3 bg-white">
        <div class="space-y-1">
            <p class="text-[14px] font-semibold leading-snug line-clamp-2">
                {{ $title }}
            </p>
        </div>
        <div class="space-y-1">
            <div class="flex items-center justify-between text-[12px] text-[#6B6F7A]">
                <span>Terkumpul</span>
                <span>Target</span>
            </div>
            <div class="h-1.5 rounded-full bg-[#ECE6C3] overflow-hidden">
                <div class="h-full rounded-full bg-[#9DAE81]" style="width: {{ $progress }}%"></div>
            </div>
            <div class="flex items-center justify-between text-[12px] text-[#23252F]">
                <span>{{ $raised }}</span>
                <span>{{ $target }}</span>
            </div>
        </div>
        <div class="pt-1 border-t border-[#F0E8C8] flex items-center gap-2 text-[12px] text-[#6B6F7A]">
            <span class="w-2 h-2 rounded-full bg-[#D5D8E2]"></span>
            <span class="truncate">{{ $organization }}</span>
        </div>
    </div>
</a>


