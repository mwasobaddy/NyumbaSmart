<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LeaseAgreement;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaseAgreement extends Component
{
    use WithFileUploads;
    
    public $unitId;
    public $unit;
    public $agreement;
    public $user;
    public $role;
    public $tenantId;
    public $tenantsList = [];
    public $agreementDocument;
    
    // Form fields
    public $startDate;
    public $endDate;
    public $rentAmount;
    public $securityDeposit;
    public $status = 'draft';
    public $notes;
    public $agreementText;
    
    // Visibility controls
    public $showForm = false;
    public $isEditing = false;
    public $showPreview = false;
    
    protected $rules = [
        'tenantId' => 'required|exists:users,id',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after:startDate',
        'rentAmount' => 'required|numeric|min:1',
        'securityDeposit' => 'required|numeric|min:0',
        'status' => 'required|in:draft,pending_tenant,pending_landlord,signed,active,terminated',
        'notes' => 'nullable|string',
        'agreementText' => 'required|string|min:100'
    ];
    
    public function mount()
    {
        $this->user = Auth::user();
        if ($this->user->hasRole('Landlord')) {
            $this->role = 'landlord';
            $this->loadTenants();
        } else {
            $this->role = 'tenant';
            $this->tenantId = $this->user->id;
        }
        
        $this->loadAgreement();
    }
    
    public function loadTenants()
    {
        $this->tenantsList = User::role('Tenant')->get();
    }
    
    public function loadAgreement()
    {
        if (!$this->unitId) return;
        
        $this->unit = Unit::findOrFail($this->unitId);
        
        $query = LeaseAgreement::where('unit_id', $this->unitId);
        
        if ($this->role == 'landlord') {
            $query->where('landlord_id', $this->user->id);
            if ($this->tenantId) {
                $query->where('tenant_id', $this->tenantId);
            }
        } else {
            $query->where('tenant_id', $this->user->id);
        }
        
        $this->agreement = $query->latest()->first();
        
        if ($this->agreement) {
            $this->fillFormFromAgreement();
        }
    }
    
    public function fillFormFromAgreement()
    {
        $this->tenantId = $this->agreement->tenant_id;
        $this->startDate = $this->agreement->start_date->format('Y-m-d');
        $this->endDate = $this->agreement->end_date->format('Y-m-d');
        $this->rentAmount = $this->agreement->rent_amount;
        $this->securityDeposit = $this->agreement->security_deposit;
        $this->status = $this->agreement->status;
        $this->notes = $this->agreement->notes;
        $this->agreementText = $this->agreement->agreement_text;
    }
    
    public function createAgreement()
    {
        if (!$this->unitId) return;
        
        $this->showForm = true;
        $this->isEditing = false;
        $this->reset(['startDate', 'endDate', 'rentAmount', 'securityDeposit', 'status', 'notes', 'agreementText']);
        
        // Set default values
        $this->startDate = Carbon::now()->format('Y-m-d');
        $this->endDate = Carbon::now()->addYear()->format('Y-m-d');
        $this->rentAmount = $this->unit->rent_amount ?? 0;
        $this->securityDeposit = $this->unit->rent_amount ?? 0;
        $this->status = 'draft';
        
        // Generate default agreement text
        $this->generateAgreementText();
    }
    
    public function editAgreement()
    {
        if (!$this->agreement) return;
        
        $this->showForm = true;
        $this->isEditing = true;
        $this->fillFormFromAgreement();
    }
    
    public function cancelForm()
    {
        $this->showForm = false;
        $this->showPreview = false;
    }
    
    public function generateAgreementText()
    {
        if (!$this->unit) return;
        
        $landlordName = $this->role === 'landlord' ? $this->user->name : ($this->unit->property->user->name ?? 'LANDLORD');
        $tenantName = $this->role === 'tenant' ? $this->user->name : (User::find($this->tenantId)->name ?? 'TENANT');
        $propertyAddress = $this->unit->property->address ?? 'PROPERTY ADDRESS';
        $unitNumber = $this->unit->unit_number ?? 'UNIT NUMBER';
        
        $today = Carbon::now()->format('F j, Y');
        $startDate = $this->startDate ? Carbon::parse($this->startDate)->format('F j, Y') : 'START DATE';
        $endDate = $this->endDate ? Carbon::parse($this->endDate)->format('F j, Y') : 'END DATE';
        $rentAmount = $this->rentAmount ?? 'RENT AMOUNT';
        $securityDeposit = $this->securityDeposit ?? 'SECURITY DEPOSIT';
        
        $this->agreementText = <<<EOT
RESIDENTIAL LEASE AGREEMENT

This Residential Lease Agreement ("Agreement") is made on {$today} between {$landlordName} ("Landlord") and {$tenantName} ("Tenant").

1. PROPERTY: The Landlord agrees to rent to the Tenant the property located at:
   {$propertyAddress}, Unit {$unitNumber} ("Premises").

2. TERM: The lease term begins on {$startDate} and ends on {$endDate}.

3. RENT: The Tenant agrees to pay {$rentAmount} KES per month, due on the 1st day of each month.

4. SECURITY DEPOSIT: The Tenant has paid {$securityDeposit} KES as a security deposit.

5. UTILITIES: The Tenant is responsible for paying all utilities and services, except for the following which will be provided by the Landlord: _______________.

6. MAINTENANCE: The Tenant agrees to maintain the Premises in good condition and promptly notify the Landlord of any necessary repairs.

7. ALTERATIONS: The Tenant shall not make alterations or improvements without prior written consent from the Landlord.

8. ENTRY BY LANDLORD: The Landlord may enter the Premises with reasonable notice for inspection, repairs, showing the property to prospective tenants or buyers.

9. TERMINATION: This Agreement may be terminated by either party with 30 days written notice prior to the end of the lease term.

10. SIGNATURES:

________________________________     ________________________________
Landlord: {$landlordName}            Date

________________________________     ________________________________
Tenant: {$tenantName}                Date
EOT;
    }
    
    public function previewAgreement()
    {
        $this->validate();
        $this->showPreview = true;
    }
    
    public function saveAgreement()
    {
        $this->validate();
        
        if ($this->isEditing && $this->agreement) {
            // Update existing agreement
            $this->agreement->update([
                'tenant_id' => $this->tenantId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'rent_amount' => $this->rentAmount,
                'security_deposit' => $this->securityDeposit,
                'status' => $this->status,
                'notes' => $this->notes,
                'agreement_text' => $this->agreementText,
            ]);
        } else {
            // Create new agreement
            $this->agreement = LeaseAgreement::create([
                'tenant_id' => $this->tenantId,
                'landlord_id' => $this->role === 'landlord' ? $this->user->id : $this->unit->property->user_id,
                'unit_id' => $this->unitId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'rent_amount' => $this->rentAmount,
                'security_deposit' => $this->securityDeposit,
                'status' => $this->status,
                'notes' => $this->notes,
                'agreement_text' => $this->agreementText,
            ]);
        }
        
        $this->showForm = false;
        $this->showPreview = false;
        $this->dispatch('agreement-updated');
        $this->dispatch('notify', [
            'message' => 'Lease agreement ' . ($this->isEditing ? 'updated' : 'created') . ' successfully',
            'type' => 'success'
        ]);
    }
    
    public function signAgreement()
    {
        if (!$this->agreement) return;
        
        if ($this->role === 'tenant' && $this->agreement->status === 'pending_tenant') {
            $this->agreement->update([
                'status' => 'pending_landlord',
                'tenant_signed_at' => now()
            ]);
            $this->status = 'pending_landlord';
        } else if ($this->role === 'landlord' && $this->agreement->status === 'pending_landlord') {
            $this->agreement->update([
                'status' => 'signed',
                'landlord_signed_at' => now()
            ]);
            $this->status = 'signed';
        } else if ($this->role === 'landlord' && $this->agreement->status === 'draft') {
            $this->agreement->update([
                'status' => 'pending_tenant'
            ]);
            $this->status = 'pending_tenant';
        }
        
        $this->dispatch('agreement-updated');
        $this->dispatch('notify', [
            'message' => 'Agreement status updated to ' . $this->status,
            'type' => 'success'
        ]);
    }
    
    public function activateAgreement()
    {
        if (!$this->agreement || $this->agreement->status !== 'signed' || $this->role !== 'landlord') return;
        
        $this->agreement->update([
            'status' => 'active',
            'activated_at' => now()
        ]);
        $this->status = 'active';
        
        $this->dispatch('agreement-updated');
        $this->dispatch('notify', [
            'message' => 'Agreement activated successfully',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        return view('livewire.onboarding.lease-agreement');
    }
}
