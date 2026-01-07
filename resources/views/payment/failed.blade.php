<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Gagal - EduSorong</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Gagal</h1>
            <p class="text-gray-600 mb-6">
                Maaf, pembayaran Anda tidak dapat diproses. Silakan coba lagi.
            </p>
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <div class="text-sm text-gray-600 space-y-2">
                    <div class="flex justify-between">
                        <span>ID Pembayaran:</span>
                        <span class="font-mono text-xs">{{ $payment->reference_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode:</span>
                        <span class="capitalize">{{ $payment->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status:</span>
                        <span class="text-red-600 font-semibold">Gagal</span>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
                <a href="{{ route('campaigns.show', $payment->campaign) }}" 
                   class="block w-full bg-[#9DAE81] text-white py-2 px-4 rounded-lg hover:bg-[#8a9a6f] transition">
                    Coba Lagi
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="block w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                    Ke Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>

