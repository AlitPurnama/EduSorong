<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $apiUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');
        $this->apiUrl = config('services.midtrans.api_url', 'https://api.sandbox.midtrans.com');
        $this->isProduction = config('services.midtrans.is_production', false);
    }

    /**
     * Create QRIS payment (Dynamic QR Code)
     *
     * @param array $data
     * @return array
     */
    public function createQRIS(array $data): array
    {
        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $data['order_id'],
                'gross_amount' => $data['amount'],
            ],
            'qris' => [
                'acquirer' => $data['acquirer'] ?? 'gopay', // gopay, shopee, dll
            ],
            'custom_expiry' => [
                'expiry_duration' => $data['expiry_duration'] ?? 24,
                'unit' => 'hour',
            ],
            'callbacks' => [
                'finish' => $data['finish_url'] ?? null,
            ],
            'item_details' => $data['item_details'] ?? [],
            'customer_details' => $data['customer_details'] ?? [],
        ];

        // Remove null values
        $payload = $this->removeNullValues($payload);

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/v2/charge", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans QRIS Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create E-Wallet charge (OVO, DANA, LINKAJA)
     *
     * @param array $data
     * @return array
     */
    public function createEWalletCharge(array $data): array
    {
        $channel = strtolower($data['channel']); // ovo, dana, linkaja

        $payload = [
            'payment_type' => 'ewallet',
            'transaction_details' => [
                'order_id' => $data['order_id'],
                'gross_amount' => $data['amount'],
            ],
            'ewallet' => [
                'store' => $channel,
            ],
            'custom_expiry' => [
                'expiry_duration' => $data['expiry_duration'] ?? 24,
                'unit' => 'hour',
            ],
            'callbacks' => [
                'finish' => $data['finish_url'] ?? null,
            ],
            'item_details' => $data['item_details'] ?? [],
            'customer_details' => $data['customer_details'] ?? [],
        ];

        // Add channel-specific parameters
        if ($channel === 'ovo' && isset($data['phone'])) {
            $payload['ewallet']['phone'] = $data['phone'];
        }

        // Remove null values
        $payload = $this->removeNullValues($payload);

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/v2/charge", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans E-Wallet Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create Virtual Account (BCA, BRI, BNI, Permata, CIMB, Mandiri, Danamon, BSI, SeaBank)
     *
     * @param array $data
     * @return array
     */
    public function createVirtualAccount(array $data): array
    {
        $bank = $data['bank'] ?? 'bca'; // Default to BCA
        
        // Map bank codes for Midtrans
        $bankMap = [
            'bca' => 'bca',
            'bri' => 'bri',
            'bni' => 'bni',
            'mandiri' => 'mandiri',
            'danamon' => 'danamon',
            'seabank' => 'seabank',
        ];
        
        $bankCode = $bankMap[strtolower($bank)] ?? 'bca';
        
        $payload = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $data['order_id'],
                'gross_amount' => $data['amount'],
            ],
            'bank_transfer' => [
                'bank' => $bankCode,
            ],
            'custom_expiry' => [
                'expiry_duration' => $data['expiry_duration'] ?? 24,
                'unit' => 'hour',
            ],
            'callbacks' => [
                'finish' => $data['finish_url'] ?? null,
            ],
            'item_details' => $data['item_details'] ?? [],
            'customer_details' => $data['customer_details'] ?? [],
        ];

        // Remove null values
        $payload = $this->removeNullValues($payload);

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/v2/charge", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Virtual Account Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get transaction status by order ID
     *
     * @param string $orderId
     * @return array
     */
    public function getTransactionStatus(string $orderId): array
    {
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->get("{$this->apiUrl}/v2/{$orderId}/status");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Get Status Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify notification signature
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    public function verifyNotificationSignature(
        string $orderId,
        string $statusCode,
        string $grossAmount,
        string $signatureKey
    ): bool {
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Remove null values from array recursively
     *
     * @param array $array
     * @return array
     */
    protected function removeNullValues(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->removeNullValues($value);
                if (empty($array[$key])) {
                    unset($array[$key]);
                }
            } elseif ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}

