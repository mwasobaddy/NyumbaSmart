<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;
use App\Models\User;
use App\Models\Unit;
use App\Models\LeaseAgreement;
use App\Models\TenantScreening;
use App\Models\PropertyInspection;
use Illuminate\Support\Facades\Auth;

class TenantOnboardingDashboard extends Component
{
    public $user;
    public $role;
    public $units = [];
    public $selectedUnitId = null;
    public $currentStep = 1;
    public $totalSteps = 3;
    
    // Onboarding records
    public $leaseAgreement = null;
    public $tenantScreening = null;
    public $propertyInspection = null;
    
    // Progress tracking
    public $steps = [
        1 => [
            'name' => 'Tenant Screening',
            'status' => 'pending',
            'description' => 'Background check, credit verification, and rental history review'
        ],
        2 => [
            'name' => 'Lease Agreement',
            'status' => 'pending',
            'description' => 'Review and sign digital lease agreement'
        ],
        3 => [
            'name' => 'Property Inspection',
            'status' => 'pending',
            'description' => 'Complete move-in inspection checklist'
        ]
    ];
    
    public function mount()
    {
        $this->user = Auth::user();
        
        // Determine role for access control
        if ($this->user->hasRole('Landlord')) {
            $this->role = 'landlord';
            $this->loadLandlordData();
        } elseif ($this->user->hasRole('Tenant')) {
            $this->role = 'tenant';
            $this->loadTenantData();
        }
        
        $this->updateStepStatuses();
    }
    
    private function loadLandlordData()
    {
        // Load properties and units owned by this landlord
        $properties = $this->user->properties;
        foreach ($properties as $property) {
            foreach ($property->units as $unit) {
                $this->units[] = $unit;
            }
        }
    }
    
    private function loadTenantData()
    {
        // For tenants, load units they're assigned to or in process of being assigned
        $this->leaseAgreement = LeaseAgreement::where('tenant_id', $this->user->id)
            ->latest()
            ->first();
            
        if ($this->leaseAgreement) {
            $this->selectedUnitId = $this->leaseAgreement->unit_id;
            
            // Load related screening and inspection
            $this->tenantScreening = TenantScreening::where('tenant_id', $this->user->id)
                ->where('unit_id', $this->selectedUnitId)
                ->latest()
                ->first();
                
            $this->propertyInspection = PropertyInspection::where('tenant_id', $this->user->id)
                ->where('unit_id', $this->selectedUnitId)
                ->where('type', 'move_in')
                ->latest()
                ->first();
        } else {
            // Check if there's a screening in progress
            $this->tenantScreening = TenantScreening::where('tenant_id', $this->user->id)
                ->latest()
                ->first();
                
            if ($this->tenantScreening) {
                $this->selectedUnitId = $this->tenantScreening->unit_id;
            }
        }
    }
    
    public function updateStepStatuses()
    {
        // Update screening status
        if ($this->tenantScreening) {
            $this->steps[1]['status'] = $this->tenantScreening->status;
            if ($this->tenantScreening->isCompleted()) {
                $this->currentStep = 2;
            }
        }
        
        // Update lease agreement status
        if ($this->leaseAgreement) {
            $this->steps[2]['status'] = $this->leaseAgreement->status;
            if ($this->leaseAgreement->isFullySigned()) {
                $this->currentStep = 3;
            }
        }
        
        // Update property inspection status
        if ($this->propertyInspection) {
            $this->steps[3]['status'] = $this->propertyInspection->status;
        }
    }
    
    public function selectUnit($unitId)
    {
        $this->selectedUnitId = $unitId;
        
        // Load existing records if any
        $this->loadUnitOnboardingRecords();
    }
    
    private function loadUnitOnboardingRecords()
    {
        if (!$this->selectedUnitId) return;
        
        // Load tenant screening for this unit
        $this->tenantScreening = TenantScreening::where('unit_id', $this->selectedUnitId)
            ->when($this->role == 'tenant', function($query) {
                $query->where('tenant_id', $this->user->id);
            })
            ->when($this->role == 'landlord', function($query) {
                $query->where('landlord_id', $this->user->id);
            })
            ->latest()
            ->first();
            
        // Load lease agreement for this unit
        $this->leaseAgreement = LeaseAgreement::where('unit_id', $this->selectedUnitId)
            ->when($this->role == 'tenant', function($query) {
                $query->where('tenant_id', $this->user->id);
            })
            ->when($this->role == 'landlord', function($query) {
                $query->where('landlord_id', $this->user->id);
            })
            ->latest()
            ->first();
            
        // Load property inspection for this unit
        $this->propertyInspection = PropertyInspection::where('unit_id', $this->selectedUnitId)
            ->where('type', 'move_in')
            ->when($this->role == 'tenant', function($query) {
                $query->where('tenant_id', $this->user->id);
            })
            ->when($this->role == 'landlord', function($query) {
                $query->where('landlord_id', $this->user->id);
            })
            ->latest()
            ->first();
            
        $this->updateStepStatuses();
    }
    
    public function render()
    {
        return view('livewire.onboarding.tenant-onboarding-dashboard');
    }
}
