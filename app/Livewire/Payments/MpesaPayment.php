<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use App\Models\Invoice;
use App\Services\DarajaService;
use Illuminate\Support\Facades\Log;

class MpesaPayment extends Component
{
    public $invoice;
    public $phone;
    public $amount;
    public $showPaymentForm = false;
    public $showPaymentResult = false;
    public $showStatusMessage = false;
    public $status = '';
    public $statusMessage = '';
    public $checkoutRequestId = null;
    public $receipt = null;
    
    protected $rules = [
        'phone' => 'required|regex:/^[0-9]{10,12}$/',
        'amount' => 'required|numeric|min:1'
    ];
    
    protected $messages = [
        'phone.required' => 'Please enter your phone number.',
        'phone.regex' => 'Phone number should be 10-12 digits without spaces or special characters.',
        'amount.required' => 'Please enter an amount.',
        'amount.numeric' => 'Amount must be a number.',
        'amount.min' => 'Amount must be at least 1.'
    ];
    
    public function mount($invoiceId = null)
    {
        if ($invoiceId) {
            $this->invoice = Invoice::findOrFail($invoiceId);
            $this->amount = $this->invoice->amount ?? 0;
        }
    }
    
    public function initiatePayment()
    {
        $this->validate();
        
        try {
            $darajaService = app(DarajaService::class);
            
            // Format phone number if needed
            $phone = $this->phone;
            if (!str_starts_with($phone, '254')) {
                $phone = '254' . substr($phone, -9);
            }
            
            // Generate reference and description
            $reference = $this->invoice ? 'INV' . $this->invoice->id : 'PAYMENT' . time();
            $description = $this->invoice ? 'Payment for Invoice #' . $this->invoice->id : 'General Payment';
            
            // Initiate STK push
            $response = $darajaService->stkPush(
                $phone,
                $this->amount,
                $reference,
                $description
            );
            
            if (isset($response['CheckoutRequestID'])) {
                $this->checkoutRequestId = $response['CheckoutRequestID'];
                $this->showStatusMessage = true;
                $this->status = 'processing';
                $this->statusMessage = 'Payment request sent to your phone. Please check your phone and enter PIN.';
                
                // In simulation mode, we'll show the payment result immediately
                $this->showPaymentResult = true;
            } else {
                $this->showStatusMessage = true;
                $this->status = 'error';
                $this->statusMessage = 'Failed to initiate payment. Please try again.';
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa Payment Error: ' . $e->getMessage());
            $this->showStatusMessage = true;
            $this->status = 'error';
            $this->statusMessage = 'An error occurred: ' . $e->getMessage();
        }
    }
    
    public function simulatePaymentSuccess()
    {
        if (!$this->checkoutRequestId) {
            $this->showStatusMessage = true;
            $this->status = 'error';
            $this->statusMessage = 'Payment not initiated yet.';
            return;
        }
        
        try {
            $darajaService = app(DarajaService::class);
            $result = $darajaService->simulateCallback($this->checkoutRequestId, true);
            
            if ($result['success']) {
                $this->status = 'success';
                $this->statusMessage = 'Payment completed successfully!';
                $this->getReceipt();
            } else {
                $this->status = 'error';
                $this->statusMessage = 'Failed to process payment: ' . ($result['message'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa Simulation Error: ' . $e->getMessage());
            $this->status = 'error';
            $this->statusMessage = 'An error occurred: ' . $e->getMessage();
        }
    }
    
    public function simulatePaymentFailure()
    {
        if (!$this->checkoutRequestId) {
            $this->showStatusMessage = true;
            $this->status = 'error';
            $this->statusMessage = 'Payment not initiated yet.';
            return;
        }
        
        try {
            $darajaService = app(DarajaService::class);
            $result = $darajaService->simulateCallback($this->checkoutRequestId, false);
            
            $this->status = 'failed';
            $this->statusMessage = 'Payment was cancelled or failed to process.';
        } catch (\Exception $e) {
            Log::error('M-Pesa Simulation Error: ' . $e->getMessage());
            $this->status = 'error';
            $this->statusMessage = 'An error occurred: ' . $e->getMessage();
        }
    }
    
    public function getReceipt()
    {
        if (!$this->checkoutRequestId) {
            return;
        }
        
        try {
            $darajaService = app(DarajaService::class);
            $this->receipt = $darajaService->generateReceipt($this->checkoutRequestId);
        } catch (\Exception $e) {
            Log::error('Receipt Generation Error: ' . $e->getMessage());
        }
    }
    
    public function togglePaymentForm()
    {
        $this->showPaymentForm = !$this->showPaymentForm;
        $this->showPaymentResult = false;
        $this->showStatusMessage = false;
    }
    
    public function resetComponent()
    {
        $this->showPaymentForm = false;
        $this->showPaymentResult = false;
        $this->showStatusMessage = false;
        $this->status = '';
        $this->statusMessage = '';
        $this->checkoutRequestId = null;
        $this->receipt = null;
    }
    
    public function render()
    {
        return view('livewire.payments.mpesa-payment');
    }
}
