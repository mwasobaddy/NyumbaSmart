<?php

namespace App\Livewire\PropertyRenovations;

use App\Models\PropertyRenovation;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class VendorManagement extends Component
{
    use WithPagination;
    
    public $renovation;
    public $renovationId;
    
    // New vendor form
    public $showVendorForm = false;
    public $name;
    public $contact_person;
    public $email;
    public $phone;
    public $business_type;
    public $address;
    public $description;
    
    // Assignment form
    public $showAssignmentForm = false;
    public $selectedVendorId;
    public $service_provided;
    public $contracted_amount;
    public $contract_date;
    
    // Search and filter
    public $search = '';
    public $showInactiveVendors = false;
    
    protected $rules = [
        // Vendor creation rules
        'name' => 'required|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'required|string|max:20',
        'business_type' => 'nullable|string|max:100',
        'address' => 'nullable|string',
        'description' => 'nullable|string',
        
        // Assignment rules
        'selectedVendorId' => 'required|exists:vendors,id',
        'service_provided' => 'required|string|max:255',
        'contracted_amount' => 'required|numeric|min:0',
        'contract_date' => 'required|date',
    ];
    
    public function mount($renovationId = null)
    {
        if ($renovationId) {
            $this->renovationId = $renovationId;
            $this->loadRenovation();
        }
        
        $this->contract_date = now()->format('Y-m-d');
    }
    
    public function loadRenovation()
    {
        $this->renovation = PropertyRenovation::with(['property', 'vendors'])
            ->findOrFail($this->renovationId);
            
        // Verify authorization
        if (!$this->authorizeAccess()) {
            session()->flash('error', 'You do not have permission to manage vendors for this renovation.');
            return redirect()->route('dashboard');
        }
    }
    
    private function authorizeAccess()
    {
        $user = Auth::user();
        
        // Check if user is admin or landlord of this property
        return $user->hasRole(['Developer', 'Admin']) ||
               ($user->hasRole('Landlord') && $user->properties->contains('id', $this->renovation->property_id));
    }
    
    public function showCreateVendorForm()
    {
        $this->reset(['name', 'contact_person', 'email', 'phone', 'business_type', 'address', 'description']);
        $this->showVendorForm = true;
    }
    
    public function cancelCreateVendor()
    {
        $this->showVendorForm = false;
    }
    
    public function createVendor()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'business_type' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        
        $vendor = Vendor::create([
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'business_type' => $this->business_type,
            'address' => $this->address,
            'description' => $this->description,
            'user_id' => Auth::id(),
            'is_active' => true,
        ]);
        
        $this->showVendorForm = false;
        $this->selectedVendorId = $vendor->id;
        
        session()->flash('message', 'Vendor created successfully. You can now assign them to this renovation.');
    }
    
    public function showAssignVendorForm()
    {
        $this->reset(['selectedVendorId', 'service_provided', 'contracted_amount']);
        $this->contract_date = now()->format('Y-m-d');
        $this->showAssignmentForm = true;
    }
    
    public function cancelAssignVendor()
    {
        $this->showAssignmentForm = false;
    }
    
    public function assignVendor()
    {
        $this->validate([
            'selectedVendorId' => 'required|exists:vendors,id',
            'service_provided' => 'required|string|max:255',
            'contracted_amount' => 'required|numeric|min:0',
            'contract_date' => 'required|date',
        ]);
        
        // Check if vendor is already assigned
        if ($this->renovation->vendors()->where('vendor_id', $this->selectedVendorId)->exists()) {
            session()->flash('error', 'This vendor is already assigned to this renovation.');
            return;
        }
        
        // Attach the vendor
        $this->renovation->vendors()->attach($this->selectedVendorId, [
            'service_provided' => $this->service_provided,
            'contracted_amount' => $this->contracted_amount,
            'paid_amount' => 0, // Initially zero
            'contract_date' => $this->contract_date,
            'status' => 'pending',
        ]);
        
        // Update renovation actual cost
        $totalContracted = $this->renovation->vendors->sum(function ($vendor) {
            return (float) $vendor->pivot->contracted_amount;
        }) + $this->contracted_amount;
        
        // Refresh renovation data
        $this->renovation->refresh();
        
        $this->showAssignmentForm = false;
        
        session()->flash('message', 'Vendor assigned successfully.');
        $this->dispatch('vendorAdded');
    }
    
    public function updateVendorStatus($vendorId, $status)
    {
        $pivotData = [
            'status' => $status
        ];
        
        if ($status === 'completed') {
            $pivotData['completion_date'] = now();
        }
        
        $this->renovation->vendors()->updateExistingPivot($vendorId, $pivotData);
        
        session()->flash('message', 'Vendor status updated successfully.');
        $this->dispatch('vendorAdded');
    }
    
    public function recordPayment($vendorId)
    {
        $vendor = $this->renovation->vendors->firstWhere('id', $vendorId);
        if (!$vendor) {
            return;
        }
        
        // Open payment form logic would go here
        // For now, we'll just simulate a full payment
        $contractedAmount = (float) $vendor->pivot->contracted_amount;
        $paidAmount = (float) $vendor->pivot->paid_amount;
        $remainingAmount = $contractedAmount - $paidAmount;
        
        if ($remainingAmount > 0) {
            $this->renovation->vendors()->updateExistingPivot($vendorId, [
                'paid_amount' => $contractedAmount,
            ]);
            
            session()->flash('message', 'Payment recorded successfully.');
        }
        
        $this->dispatch('vendorAdded');
    }
    
    public function removeVendor($vendorId)
    {
        // Check if vendor has any expenses connected
        $hasExpenses = $this->renovation->expenses()
            ->where('vendor_id', $vendorId)
            ->exists();
        
        if ($hasExpenses) {
            session()->flash('error', 'Cannot remove vendor because there are expenses associated with them. Update the expenses first.');
            return;
        }
        
        $this->renovation->vendors()->detach($vendorId);
        session()->flash('message', 'Vendor removed from this renovation.');
        $this->dispatch('vendorAdded');
    }
    
    public function render()
    {
        // Query to get available vendors (not already assigned)
        $assignedVendorIds = $this->renovation ? $this->renovation->vendors->pluck('id')->toArray() : [];
        
        $vendorQuery = Vendor::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when(!$this->showInactiveVendors, function ($query) {
                $query->where('is_active', true);
            });
        
        $availableVendors = clone $vendorQuery;
        $availableVendors = $availableVendors->whereNotIn('id', $assignedVendorIds)->get();
        
        return view('livewire.property-renovations.vendor-management', [
            'availableVendors' => $availableVendors,
        ]);
    }
}
