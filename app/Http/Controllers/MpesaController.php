<?php

namespace App\Http\Controllers;

use App\Services\DarajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class MpesaController extends Controller
{
    protected $darajaService;
    
    public function __construct(DarajaService $darajaService) 
    {
        $this->darajaService = $darajaService;
    }
    
    /**
     * Initiate STK Push request
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'invoice_id' => 'sometimes|exists:invoices,id'
        ]);
        
        try {
            $phone = $request->phone;
            // Format the phone number if needed (e.g., add country code)
            if (!str_starts_with($phone, '254')) {
                $phone = '254' . substr($phone, -9);
            }
            
            // Get description and reference
            $description = 'Payment for ' . ($request->description ?? 'Rental Services');
            $accountRef = $request->reference ?? 'INV' . ($request->invoice_id ?? time());
            
            // Initiate the STK push
            $response = $this->darajaService->stkPush(
                $phone, 
                $request->amount, 
                $accountRef, 
                $description
            );
            
            // Store information about the payment attempt in the session
            if (isset($response['CheckoutRequestID'])) {
                session()->put('mpesa_checkout_request_id', $response['CheckoutRequestID']);
                session()->put('mpesa_payment_details', [
                    'amount' => $request->amount,
                    'phone' => $phone,
                    'invoice_id' => $request->invoice_id ?? null,
                    'description' => $description,
                    'account_reference' => $accountRef
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Payment request initiated successfully',
                'data' => $response
            ]);
            
        } catch (\Exception $e) {
            Log::error('STK Push Initiation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process callback from M-Pesa
     */
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback Received', $request->all());
        
        // In a real implementation, we would process the callback data here
        // For now, we'll just acknowledge receipt
        
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Callback received successfully'
        ]);
    }
    
    /**
     * Simulate a callback for simulated payments
     */
    public function simulateCallback(Request $request)
    {
        $request->validate([
            'checkout_request_id' => 'required|string',
            'success' => 'required|boolean'
        ]);
        
        $result = $this->darajaService->simulateCallback(
            $request->checkout_request_id,
            $request->success
        );
        
        if (!$result['success'] && $result['message'] === 'Transaction not found') {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
        
        // If the transaction was successful, record it in the database
        if ($result['success']) {
            $this->recordSuccessfulPayment($request->checkout_request_id, $result['transaction']);
        }
        
        return response()->json([
            'success' => true,
            'message' => $request->success ? 'Payment completed successfully' : 'Payment failed',
            'data' => $result
        ]);
    }
    
    /**
     * Check the status of a transaction
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'checkout_request_id' => 'required|string'
        ]);
        
        $status = $this->darajaService->checkTransactionStatus($request->checkout_request_id);
        
        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }
    
    /**
     * Generate a receipt for a completed payment
     */
    public function generateReceipt(Request $request)
    {
        $request->validate([
            'checkout_request_id' => 'required|string'
        ]);
        
        $receipt = $this->darajaService->generateReceipt($request->checkout_request_id);
        
        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not available for this transaction'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $receipt
        ]);
    }
    
    /**
     * Show the payment simulation interface
     */
    public function showSimulator()
    {
        return view('mpesa.simulator');
    }
    
    /**
     * Record a successful payment in the database
     */
    protected function recordSuccessfulPayment($checkoutRequestId, $transaction)
    {
        try {
            // Create a payment record
            $payment = new Payment();
            $payment->transaction_id = $transaction['receipt_number'];
            $payment->amount = $transaction['amount'];
            $payment->phone = $transaction['phone'];
            $payment->description = $transaction['description'];
            $payment->status = 'completed';
            $payment->payment_method = 'mpesa';
            $payment->payment_date = now();
            
            // Link to invoice if available
            if (isset($transaction['reference']) && str_starts_with($transaction['reference'], 'INV')) {
                $invoiceId = substr($transaction['reference'], 3);
                if (is_numeric($invoiceId)) {
                    $invoice = Invoice::find($invoiceId);
                    if ($invoice) {
                        $payment->invoice_id = $invoice->id;
                        $payment->user_id = $invoice->user_id;
                        
                        // Update invoice status
                        $invoice->status = 'paid';
                        $invoice->payment_date = now();
                        $invoice->save();
                    }
                }
            } else {
                $payment->user_id = Auth::id();
            }
            
            $payment->save();
            
            // Record the transaction ID in the session
            session()->put('last_payment_id', $payment->id);
            
            Log::info('Payment recorded successfully', [
                'checkout_request_id' => $checkoutRequestId,
                'payment_id' => $payment->id
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to record payment: ' . $e->getMessage(), [
                'checkout_request_id' => $checkoutRequestId,
                'transaction' => $transaction
            ]);
            
            return false;
        }
    }
}