<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Create QRIS payment
     */
    public function createQRIS(Request $request, Campaign $campaign)
    {
        try {
            $isGuest = $request->input('is_guest', false) || !Auth::check();
            
            $rules = [
                'amount' => 'required|integer|min:10000',
                'donor_message' => 'nullable|string|max:1000',
                'is_anonymous' => 'nullable|boolean',
                'is_guest' => 'nullable|boolean',
            ];
            
            // Add guest validation rules only if guest
            if ($isGuest) {
                $rules['donor_name'] = 'required|string|max:255';
                $rules['donor_phone'] = 'required|string|max:20';
                $rules['donor_email'] = 'required|email|max:255';
            }
            
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'error' => $e->errors(),
            ], 422);
        }

        $amount = $request->input('amount');
        $orderId = 'QRIS-' . time() . '-' . Str::random(6);
        $isGuest = $request->input('is_guest', false) || !Auth::check();

        // Get customer details
        $customerName = $isGuest 
            ? $request->input('donor_name')
            : (Auth::user()->name ?? 'Guest');
        $customerEmail = $isGuest
            ? $request->input('donor_email')
            : (Auth::user()->email ?? 'guest@example.com');
        $customerPhone = $isGuest
            ? $request->input('donor_phone')
            : (Auth::user()->phone ?? null);

        // Determine donor name for logged-in users
        $isAnonymous = $request->input('is_anonymous', false);
        $donorName = $isGuest 
            ? $request->input('donor_name')
            : ($isAnonymous ? (Auth::user()->name ?? null) : null);

        // Create payment record
        $payment = Payment::create([
            'campaign_id' => $campaign->id,
            'user_id' => Auth::id(),
            'donor_name' => $donorName,
            'donor_phone' => $isGuest ? $request->input('donor_phone') : null,
            'donor_email' => $isGuest ? $request->input('donor_email') : null,
            'donor_message' => $request->input('donor_message'),
            'is_anonymous' => $isAnonymous,
            'midtrans_order_id' => $orderId,
            'payment_method' => 'qris',
            'status' => 'pending',
            'amount' => $amount,
            'reference_id' => $orderId,
            'expires_at' => now()->addHours(24),
        ]);

        // Call Midtrans API
        $result = $this->midtransService->createQRIS([
            'order_id' => $orderId,
            'amount' => $amount,
            'acquirer' => 'gopay', // Default to gopay
            'expiry_duration' => 24,
            'finish_url' => route('payment.success', $payment->id),
            'item_details' => [
                [
                    'id' => 'donation-' . $campaign->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Donasi: ' . $campaign->title,
                ],
            ],
            'customer_details' => array_filter([
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ]),
        ]);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            
            $errorMessage = 'Gagal membuat pembayaran QRIS';
            if (isset($result['error'])) {
                if (is_array($result['error'])) {
                    $errorMessage .= ': ' . json_encode($result['error']);
                } else {
                    $errorMessage .= ': ' . $result['error'];
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error' => $result['error'] ?? 'Unknown error',
            ], 400);
        }

        $midtransData = $result['data'];

        // Update payment with Midtrans data
        $payment->update([
            'midtrans_transaction_id' => $midtransData['transaction_id'] ?? null,
            'qr_string' => $midtransData['actions'][0]['url'] ?? null,
            'qr_url' => isset($midtransData['actions'][0]['url']) 
                ? "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($midtransData['actions'][0]['url'])
                : null,
            'status' => $this->mapMidtransStatus($midtransData['transaction_status'] ?? 'pending'),
            'transaction_status' => $midtransData['transaction_status'] ?? 'pending',
            'expires_at' => isset($midtransData['expiry_time']) 
                ? now()->parse($midtransData['expiry_time']) 
                : now()->addHours(24),
        ]);

        return response()->json([
            'success' => true,
            'payment' => $payment->fresh(),
            'qr_string' => $payment->qr_string,
            'qr_url' => $payment->qr_url,
        ]);
    }

    /**
     * Create E-Wallet charge (OVO, DANA, LINKAJA)
     */
    public function createEWallet(Request $request, Campaign $campaign)
    {
        try {
            $isGuest = $request->input('is_guest', false) || !Auth::check();
            
            $rules = [
                'amount' => 'required|integer|min:10000',
                'channel' => 'required|in:ovo,dana,linkaja',
                'phone' => 'nullable|string',
                'donor_message' => 'nullable|string|max:1000',
                'is_anonymous' => 'nullable|boolean',
                'is_guest' => 'nullable|boolean',
            ];
            
            // Add guest validation rules only if guest
            if ($isGuest) {
                $rules['donor_name'] = 'required|string|max:255';
                $rules['donor_phone'] = 'required|string|max:20';
                $rules['donor_email'] = 'required|email|max:255';
            }
            
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'error' => $e->errors(),
            ], 422);
        }

        $amount = $request->input('amount');
        $channel = $request->input('channel');
        $orderId = 'EWALLET-' . time() . '-' . Str::random(6);

        // Get customer details
        $customerName = $isGuest 
            ? $request->input('donor_name')
            : (Auth::user()->name ?? 'Guest');
        $customerEmail = $isGuest
            ? $request->input('donor_email')
            : (Auth::user()->email ?? 'guest@example.com');
        $customerPhone = $request->input('phone') 
            ?? ($isGuest ? $request->input('donor_phone') : (Auth::user()->phone ?? null));

        // Determine donor name for logged-in users
        $isAnonymous = $request->input('is_anonymous', false);
        $donorName = $isGuest 
            ? $request->input('donor_name')
            : ($isAnonymous ? (Auth::user()->name ?? null) : null);

        // Create payment record
        $payment = Payment::create([
            'campaign_id' => $campaign->id,
            'user_id' => Auth::id(),
            'donor_name' => $donorName,
            'donor_phone' => $isGuest ? $request->input('donor_phone') : null,
            'donor_email' => $isGuest ? $request->input('donor_email') : null,
            'donor_message' => $request->input('donor_message'),
            'is_anonymous' => $isAnonymous,
            'midtrans_order_id' => $orderId,
            'payment_method' => 'ewallet',
            'payment_channel' => $channel,
            'status' => 'pending',
            'amount' => $amount,
            'reference_id' => $orderId,
            'expires_at' => now()->addHours(24),
        ]);

        // Call Midtrans API
        $result = $this->midtransService->createEWalletCharge([
            'order_id' => $orderId,
            'amount' => $amount,
            'channel' => $channel,
            'phone' => $request->input('phone'),
            'expiry_duration' => 24,
            'finish_url' => route('payment.success', $payment->id),
            'item_details' => [
                [
                    'id' => 'donation-' . $campaign->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Donasi: ' . $campaign->title,
                ],
            ],
            'customer_details' => array_filter([
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ]),
        ]);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pembayaran E-Wallet',
                'error' => $result['error'] ?? 'Unknown error',
            ], 400);
        }

        $midtransData = $result['data'];

        // Update payment with Midtrans data
        $payment->update([
            'midtrans_transaction_id' => $midtransData['transaction_id'] ?? null,
            'deeplink_url' => $midtransData['actions'][0]['url'] ?? null,
            'status' => $this->mapMidtransStatus($midtransData['transaction_status'] ?? 'pending'),
            'transaction_status' => $midtransData['transaction_status'] ?? 'pending',
        ]);

        return response()->json([
            'success' => true,
            'payment' => $payment->fresh(),
            'deeplink_url' => $payment->deeplink_url,
        ]);
    }

    /**
     * Create BCA Virtual Account
     */
    public function createVirtualAccount(Request $request, Campaign $campaign)
    {
        try {
            $isGuest = $request->input('is_guest', false) || !Auth::check();
            
            $rules = [
                'amount' => 'required|integer|min:10000',
                'bank' => 'required|in:bca,bri,bni,mandiri,danamon,seabank',
                'donor_message' => 'nullable|string|max:1000',
                'is_anonymous' => 'nullable|boolean',
                'is_guest' => 'nullable|boolean',
            ];
            
            // Add guest validation rules only if guest
            if ($isGuest) {
                $rules['donor_name'] = 'required|string|max:255';
                $rules['donor_phone'] = 'required|string|max:20';
                $rules['donor_email'] = 'required|email|max:255';
            } else {
                $rules['name'] = 'nullable|string|max:255';
            }
            
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'error' => $e->errors(),
            ], 422);
        }

        $amount = $request->input('amount');
        $bank = $request->input('bank', 'bca');
        $name = $isGuest 
            ? $request->input('donor_name')
            : ($request->input('name') ?? (Auth::user()->name ?? 'Guest'));
        $orderId = 'VA-' . strtoupper($bank) . '-' . time() . '-' . Str::random(6);

        // Get customer details
        $customerName = $name;
        $customerEmail = $isGuest
            ? $request->input('donor_email')
            : (Auth::user()->email ?? 'guest@example.com');
        $customerPhone = $isGuest
            ? $request->input('donor_phone')
            : (Auth::user()->phone ?? null);

        // Determine donor name for logged-in users
        $isAnonymous = $request->input('is_anonymous', false);
        $donorName = $isGuest 
            ? $request->input('donor_name')
            : ($isAnonymous ? (Auth::user()->name ?? null) : null);

        // Create payment record
        $payment = Payment::create([
            'campaign_id' => $campaign->id,
            'user_id' => Auth::id(),
            'donor_name' => $donorName,
            'donor_phone' => $isGuest ? $request->input('donor_phone') : null,
            'donor_email' => $isGuest ? $request->input('donor_email') : null,
            'donor_message' => $request->input('donor_message'),
            'is_anonymous' => $isAnonymous,
            'midtrans_order_id' => $orderId,
            'payment_method' => 'virtual_account',
            'payment_channel' => $bank,
            'status' => 'pending',
            'amount' => $amount,
            'reference_id' => $orderId,
            'expires_at' => now()->addDays(1),
        ]);

        // Call Midtrans API
        $result = $this->midtransService->createVirtualAccount([
            'order_id' => $orderId,
            'amount' => $amount,
            'bank' => $bank,
            'expiry_duration' => 24,
            'finish_url' => route('payment.success', $payment->id),
            'item_details' => [
                [
                    'id' => 'donation-' . $campaign->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Donasi: ' . $campaign->title,
                ],
            ],
            'customer_details' => array_filter([
                'first_name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ]),
        ]);

        if (!$result['success']) {
            $payment->update(['status' => 'failed']);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Virtual Account',
                'error' => $result['error'] ?? 'Unknown error',
            ], 400);
        }

        $midtransData = $result['data'];

        // Update payment with Midtrans data
        $payment->update([
            'midtrans_transaction_id' => $midtransData['transaction_id'] ?? null,
            'virtual_account_number' => $midtransData['va_numbers'][0]['va_number'] ?? null,
            'status' => $this->mapMidtransStatus($midtransData['transaction_status'] ?? 'pending'),
            'transaction_status' => $midtransData['transaction_status'] ?? 'pending',
            'expires_at' => isset($midtransData['expiry_time']) 
                ? now()->parse($midtransData['expiry_time']) 
                : now()->addDays(1),
        ]);

        return response()->json([
            'success' => true,
            'payment' => $payment->fresh(),
            'virtual_account_number' => $payment->virtual_account_number,
        ]);
    }

    /**
     * Get payment status
     */
    public function getStatus(Payment $payment)
    {
        // Sync with Midtrans
        if ($payment->midtrans_order_id) {
            $result = $this->midtransService->getTransactionStatus($payment->midtrans_order_id);
            
            if ($result['success']) {
                $midtransData = $result['data'];
                $payment->update([
                    'midtrans_transaction_id' => $midtransData['transaction_id'] ?? $payment->midtrans_transaction_id,
                    'status' => $this->mapMidtransStatus($midtransData['transaction_status'] ?? $payment->status),
                    'transaction_status' => $midtransData['transaction_status'] ?? $payment->transaction_status,
                    'notification_data' => $midtransData,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'payment' => $payment->fresh(),
        ]);
    }

    /**
     * Payment success page
     */
    public function success(Payment $payment)
    {
        return view('payment.success', compact('payment'));
    }

    /**
     * Payment failed page
     */
    public function failed(Payment $payment)
    {
        return view('payment.failed', compact('payment'));
    }

    /**
     * Map Midtrans status to our status
     */
    protected function mapMidtransStatus(string $midtransStatus): string
    {
        $statusMap = [
            'pending' => 'pending',
            'settlement' => 'paid',
            'capture' => 'paid',
            'authorize' => 'pending',
            'deny' => 'failed',
            'cancel' => 'cancel',
            'expire' => 'expired',
            'refund' => 'failed',
        ];

        return $statusMap[strtolower($midtransStatus)] ?? 'pending';
    }
}

