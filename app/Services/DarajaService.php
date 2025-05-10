<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DarajaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortcode;
    protected $passkey;
    protected $environment;
    protected $callbackUrl;
    protected $simulationMode;

    public function __construct()
    {
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->passkey = config('services.mpesa.passkey');
        $this->environment = config('services.mpesa.environment');
        $this->callbackUrl = config('services.mpesa.callback_url');
        $this->simulationMode = config('services.mpesa.simulation_mode', true);
    }

    protected function getBaseUrl()
    {
        return $this->environment === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    public function getAccessToken()
    {
        if ($this->simulationMode) {
            return 'simulated-access-token-' . Str::random(40);
        }

        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get($this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials');

        return $response->json('access_token');
    }

    public function stkPush($phone, $amount, $accountRef = null, $description = null)
    {
        $token = $this->getAccessToken();
        $timestamp = now()->format('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        
        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $accountRef ?? $phone,
            'TransactionDesc' => $description ?? 'Payment',
        ];

        if ($this->simulationMode) {
            return $this->simulateStkPush($phone, $amount, $accountRef, $description);
        }

        return Http::withToken($token)
            ->post($this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest', $payload)
            ->throw()
            ->json();
    }

    /**
     * Simulate STK Push request
     */
    protected function simulateStkPush($phone, $amount, $accountRef = null, $description = null)
    {
        // Generate a unique CheckoutRequestID
        $checkoutRequestId = 'ws_CO_' . now()->format('YmdHis') . Str::random(10);
        
        // Log the simulated request
        Log::info('Simulated M-Pesa STK Push', [
            'phone' => $phone,
            'amount' => $amount,
            'reference' => $accountRef,
            'description' => $description,
            'CheckoutRequestID' => $checkoutRequestId
        ]);

        // Store the transaction in session for simulation
        session()->put("mpesa_simulation.{$checkoutRequestId}", [
            'phone' => $phone,
            'amount' => $amount,
            'reference' => $accountRef,
            'description' => $description,
            'timestamp' => now()->toDateTimeString(),
            'status' => 'pending'
        ]);

        // Return a simulated success response
        return [
            'MerchantRequestID' => 'sim_' . Str::random(20),
            'CheckoutRequestID' => $checkoutRequestId,
            'ResponseCode' => '0',
            'ResponseDescription' => 'Success. Request accepted for processing',
            'CustomerMessage' => 'Success. Request accepted for processing'
        ];
    }

    /**
     * Simulate STK Push Callback
     */
    public function simulateCallback($checkoutRequestId, $success = true)
    {
        $transaction = session()->get("mpesa_simulation.{$checkoutRequestId}");
        
        if (!$transaction) {
            return [
                'success' => false,
                'message' => 'Transaction not found'
            ];
        }

        $response = [];

        if ($success) {
            // Simulate a successful transaction
            $mpesaReceiptNumber = 'SIM' . strtoupper(Str::random(8));
            
            $response = [
                'Body' => [
                    'stkCallback' => [
                        'MerchantRequestID' => 'sim_' . Str::random(20),
                        'CheckoutRequestID' => $checkoutRequestId,
                        'ResultCode' => 0,
                        'ResultDesc' => 'The service request is processed successfully.',
                        'CallbackMetadata' => [
                            'Item' => [
                                [
                                    'Name' => 'Amount',
                                    'Value' => $transaction['amount']
                                ],
                                [
                                    'Name' => 'MpesaReceiptNumber',
                                    'Value' => $mpesaReceiptNumber
                                ],
                                [
                                    'Name' => 'TransactionDate',
                                    'Value' => now()->format('YmdHis')
                                ],
                                [
                                    'Name' => 'PhoneNumber',
                                    'Value' => $transaction['phone']
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            
            // Update the transaction status
            $transaction['status'] = 'completed';
            $transaction['receipt_number'] = $mpesaReceiptNumber;
            $transaction['completed_at'] = now()->toDateTimeString();
            
        } else {
            // Simulate a failed transaction
            $response = [
                'Body' => [
                    'stkCallback' => [
                        'MerchantRequestID' => 'sim_' . Str::random(20),
                        'CheckoutRequestID' => $checkoutRequestId,
                        'ResultCode' => 1032,
                        'ResultDesc' => 'Transaction cancelled by user.',
                    ]
                ]
            ];
            
            // Update the transaction status
            $transaction['status'] = 'failed';
            $transaction['failure_reason'] = 'Transaction cancelled by user';
        }
        
        // Update the transaction in session
        session()->put("mpesa_simulation.{$checkoutRequestId}", $transaction);
        
        return [
            'success' => $success,
            'response' => $response,
            'transaction' => $transaction
        ];
    }

    /**
     * Get a simulated transaction
     */
    public function getSimulatedTransaction($checkoutRequestId)
    {
        return session()->get("mpesa_simulation.{$checkoutRequestId}");
    }

    /**
     * Check transaction status
     */
    public function checkTransactionStatus($checkoutRequestId)
    {
        if ($this->simulationMode) {
            $transaction = $this->getSimulatedTransaction($checkoutRequestId);
            return [
                'CheckoutRequestID' => $checkoutRequestId,
                'status' => $transaction['status'] ?? 'not_found',
                'transaction' => $transaction
            ];
        }
        
        // Implement actual transaction status check here for non-simulation mode
        // This would use the M-Pesa API to check the status of an actual transaction
        return null;
    }

    /**
     * Generate a payment receipt
     */
    public function generateReceipt($checkoutRequestId)
    {
        $transaction = $this->simulationMode ? 
            $this->getSimulatedTransaction($checkoutRequestId) : 
            null; // In real mode, this would fetch transaction details from the database

        if (!$transaction || $transaction['status'] !== 'completed') {
            return null;
        }

        return [
            'receipt_number' => $transaction['receipt_number'],
            'phone' => $transaction['phone'],
            'amount' => $transaction['amount'],
            'reference' => $transaction['reference'],
            'description' => $transaction['description'],
            'date' => $transaction['completed_at'],
            'generated_at' => now()->toDateTimeString()
        ];
    }
}
