<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle Midtrans notification/webhook
     * 
     * Midtrans akan mengirim POST request dengan transaction status
     */
    public function handle(Request $request)
    {
        // Log notification untuk debugging
        Log::info('Midtrans Notification Received', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $signatureKey = $request->input('signature_key');

        // Verify signature (optional but recommended)
        // Note: We still return 200 even if signature is invalid to prevent Midtrans retries
        // But we log the warning for security monitoring
        if ($signatureKey && !$this->midtransService->verifyNotificationSignature(
            $orderId,
            $statusCode,
            $grossAmount,
            $signatureKey
        )) {
            Log::warning('Midtrans Notification: Invalid signature', [
                'order_id' => $orderId,
            ]);
            // Return 200 to prevent Midtrans from retrying, but log the issue
            return response()->json(['status' => 'received', 'message' => 'Invalid signature logged'], 200);
        }

        if (!$orderId) {
            Log::warning('Midtrans Notification: Missing order_id', [
                'request_data' => $request->all(),
            ]);
            // Return 200 to prevent Midtrans from retrying
            return response()->json(['status' => 'received', 'message' => 'Missing order_id'], 200);
        }

        try {
            DB::beginTransaction();

            // Find payment by order_id
            $payment = Payment::where('midtrans_order_id', $orderId)->first();

            if (!$payment) {
                Log::warning('Midtrans Notification: Payment not found', ['order_id' => $orderId]);
                DB::rollBack();
                // Return 200 to prevent Midtrans from retrying, but log the issue
                return response()->json(['status' => 'received', 'message' => 'Payment not found'], 200);
            }

            // Update payment status
            $mappedStatus = $this->mapMidtransStatus($transactionStatus);

            $updateData = [
                'midtrans_transaction_id' => $request->input('transaction_id') ?? $payment->midtrans_transaction_id,
                'status' => $mappedStatus,
                'transaction_status' => $transactionStatus,
                'notification_data' => $request->all(),
            ];

            // Update virtual account number if available
            if ($request->has('va_numbers') && !empty($request->input('va_numbers'))) {
                $updateData['virtual_account_number'] = $request->input('va_numbers')[0]['va_number'] ?? $payment->virtual_account_number;
            }

            // If payment is settled/paid, update campaign
            if (($mappedStatus === 'paid' || $transactionStatus === 'settlement') && $payment->status !== 'paid') {
                $updateData['paid_at'] = now();
                
                // Update campaign raised amount only if not already paid
                if ($payment->status !== 'paid') {
                    $campaign = $payment->campaign;
                    $campaign->increment('raised_amount', $payment->amount);
                }
            }

            $payment->update($updateData);

            // Clear donation feed cache when payment is successful
            if ($mappedStatus === 'paid' || $transactionStatus === 'settlement') {
                Cache::forget('recent_donations_feed');
            }

            DB::commit();

            Log::info('Midtrans Notification: Payment updated', [
                'payment_id' => $payment->id,
                'order_id' => $orderId,
                'status' => $mappedStatus,
            ]);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Midtrans Notification Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            // Always return 200 to prevent Midtrans from retrying
            // The error is logged for investigation
            return response()->json([
                'status' => 'received',
                'message' => 'Error logged, will be processed manually'
            ], 200);
        }
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

