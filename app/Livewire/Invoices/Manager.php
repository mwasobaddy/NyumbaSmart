<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Payment;
use App\Services\DarajaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Manager extends Component
{
    public $properties;
    public $selectedProperty;
    public $units = [];
    public $invoices;
    public $is_landlord;
    
    // Form fields
    public $unit_id;
    public $amount;
    public $due_date;
    public $invoice_type = 'rent';
    public $penalty = 0;
    public $additional_notes;
    public $invoice_id;
    
    // Payment fields
    public $phone;
    public $payment_method = 'mpesa';
    public $payment_invoice_id;
    
    protected $rules = [
        'unit_id' => 'required|exists:units,id',
        'amount' => 'required|numeric|min:1',
        'due_date' => 'required|date',
        'penalty' => 'nullable|numeric|min:0',
        'additional_notes' => 'nullable|string',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->is_landlord = $user->hasRole('Landlord');
        
        if ($this->is_landlord) {
            $this->properties = $user->properties;
            $this->loadInvoices();
        } else {
            // Tenant sees their own invoices
            $this->invoices = $user->invoices()->with(['unit.property'])->latest()->get();
        }
    }
    
    public function updatedSelectedProperty($value)
    {
        if ($value) {
            $this->units = Unit::where('property_id', $value)->get();
        } else {
            $this->units = [];
        }
    }
    
    public function loadInvoices()
    {
        if ($this->is_landlord) {
            $query = Invoice::whereHas('unit', function($q) {
                $q->whereIn('property_id', $this->properties->pluck('id'));
            })->with(['user', 'unit.property']);
            
            if ($this->selectedProperty) {
                $query->whereHas('unit', function($q) {
                    $q->where('property_id', $this->selectedProperty);
                });
            }
            
            $this->invoices = $query->latest()->get();
        }
    }
    
    public function createInvoice()
    {
        if (!$this->is_landlord) {
            session()->flash('error', 'Only landlords can create invoices.');
            return;
        }
        
        $this->validate();
        
        $unit = Unit::findOrFail($this->unit_id);
        $tenant = $unit->invoices()->where('status', '!=', 'cancelled')->first()?->user;
        
        if (!$tenant) {
            session()->flash('error', 'No tenant found for this unit. Please assign a tenant first.');
            return;
        }
        
        $invoice = new Invoice([
            'user_id' => $tenant->id,
            'unit_id' => $this->unit_id,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'status' => 'pending',
            'penalty' => $this->penalty,
        ]);
        
        $invoice->save();
        $this->resetForm();
        $this->loadInvoices();
        
        session()->flash('status', 'Invoice created successfully and sent to tenant.');
    }
    
    public function editInvoice($id)
    {
        if (!$this->is_landlord) {
            return;
        }
        
        $invoice = Invoice::findOrFail($id);
        $this->invoice_id = $invoice->id;
        $this->unit_id = $invoice->unit_id;
        $this->amount = $invoice->amount;
        $this->due_date = $invoice->due_date;
        $this->penalty = $invoice->penalty;
        
        // Set selected property for the dropdown
        $this->selectedProperty = $invoice->unit->property_id;
        $this->updatedSelectedProperty($this->selectedProperty);
    }
    
    public function updateInvoice()
    {
        if (!$this->is_landlord) {
            return;
        }
        
        $this->validate();
        
        $invoice = Invoice::findOrFail($this->invoice_id);
        
        if ($invoice->status === 'paid') {
            session()->flash('error', 'Cannot update a paid invoice.');
            return;
        }
        
        $invoice->update([
            'unit_id' => $this->unit_id,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'penalty' => $this->penalty,
        ]);
        
        $this->resetForm();
        $this->loadInvoices();
        session()->flash('status', 'Invoice updated successfully.');
    }
    
    public function cancelInvoice($id)
    {
        if (!$this->is_landlord) {
            return;
        }
        
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status === 'paid') {
            session()->flash('error', 'Cannot cancel a paid invoice.');
            return;
        }
        
        $invoice->update(['status' => 'cancelled']);
        $this->loadInvoices();
        session()->flash('status', 'Invoice cancelled successfully.');
    }
    
    public function initiatePayment($id)
    {
        if ($this->is_landlord) {
            return;
        }
        
        $this->payment_invoice_id = $id;
        $invoice = Invoice::findOrFail($id);
        $this->amount = $invoice->amount; // Set payment amount
        
        // If there's a penalty and the invoice is overdue
        if ($invoice->penalty > 0 && Carbon::parse($invoice->due_date)->isPast()) {
            $this->amount += $invoice->penalty;
        }
    }
    
    public function processPayment(DarajaService $daraja)
    {
        if ($this->is_landlord) {
            return;
        }
        
        $this->validate([
            'phone' => 'required|regex:/^(07|01)[0-9]{8}$/',
            'payment_method' => 'required|in:mpesa',
        ]);
        
        $invoice = Invoice::findOrFail($this->payment_invoice_id);
        
        try {
            // Format phone to international format
            $phone = '+254' . substr($this->phone, 1);
            
            $accountRef = 'INV-' . $invoice->id;
            $description = 'Payment for ' . $invoice->unit->property->name . ' Unit ' . $invoice->unit->unit_number;
            
            $response = $daraja->stkPush($phone, $this->amount, $accountRef, $description);
            
            // Record payment attempt
            $payment = new Payment([
                'user_id' => Auth::id(),
                'invoice_id' => $invoice->id,
                'amount' => $this->amount,
                'method' => 'mpesa',
                'status' => 'pending',
                'transaction_id' => $response['CheckoutRequestID'] ?? null,
            ]);
            
            $payment->save();
            
            $this->resetPaymentForm();
            session()->flash('status', 'Payment initiated. Please check your phone to complete the transaction.');
            
        } catch (\Exception $e) {
            Log::error('M-Pesa payment error: ' . $e->getMessage());
            session()->flash('error', 'Payment failed. Please try again later.');
        }
    }
    
    private function resetForm()
    {
        $this->invoice_id = null;
        $this->unit_id = '';
        $this->amount = '';
        $this->due_date = '';
        $this->penalty = 0;
        $this->additional_notes = '';
    }
    
    private function resetPaymentForm()
    {
        $this->payment_invoice_id = null;
        $this->phone = '';
        $this->payment_method = 'mpesa';
    }

    public function render()
    {
        return view('livewire.invoices.manager');
    }
}
