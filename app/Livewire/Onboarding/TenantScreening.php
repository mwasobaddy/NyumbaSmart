<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;
use App\Models\TenantScreening;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class TenantScreening extends Component
{
    use WithFileUploads;
    
    public $unitId;
    public $unit;
    public $user;
    public $role;
    public $screening;
    public $tenantId;
    public $tenantsList = [];
    public $documents = [];
    
    // Form fields
    public $creditCheckPassed = false;
    public $backgroundCheckPassed = false;
    public $evictionCheckPassed = false;
    public $employmentVerified = false;
    public $incomeVerified = false;
    public $notes;
    public $status = 'pending';
    
    // Visibility controls
    public $showForm = false;
    public $isEditing = false;
    
    protected $rules = [
        'tenantId' => 'required|exists:users,id',
        'creditCheckPassed' => 'boolean',
        'backgroundCheckPassed' => 'boolean',
        'evictionCheckPassed' => 'boolean',
        'employmentVerified' => 'boolean',
        'incomeVerified' => 'boolean',
        'notes' => 'nullable|string',
        'status' => 'required|in:pending,in_progress,completed,rejected'
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
        
        $this->loadScreening();
    }
    
    public function loadTenants()
    {
        $this->tenantsList = User::role('Tenant')->get();
    }
    
    public function loadScreening()
    {
        if (!$this->unitId) return;
        
        $this->unit = Unit::findOrFail($this->unitId);
        
        $query = TenantScreening::where('unit_id', $this->unitId);
        
        if ($this->role == 'landlord') {
            $query->where('landlord_id', $this->user->id);
            if ($this->tenantId) {
                $query->where('tenant_id', $this->tenantId);
            }
        } else {
            $query->where('tenant_id', $this->user->id);
        }
        
        $this->screening = $query->latest()->first();
        
        if ($this->screening) {
            $this->fillFormFromScreening();
        }
    }
    
    public function fillFormFromScreening()
    {
        $this->tenantId = $this->screening->tenant_id;
        $this->creditCheckPassed = $this->screening->credit_check_passed;
        $this->backgroundCheckPassed = $this->screening->background_check_passed;
        $this->evictionCheckPassed = $this->screening->eviction_check_passed;
        $this->employmentVerified = $this->screening->employment_verified;
        $this->incomeVerified = $this->screening->income_verified;
        $this->notes = $this->screening->notes;
        $this->status = $this->screening->status;
    }
    
    public function createScreening()
    {
        if (!$this->unitId) return;
        
        $this->showForm = true;
        $this->isEditing = false;
        $this->reset(['creditCheckPassed', 'backgroundCheckPassed', 'evictionCheckPassed', 'employmentVerified', 'incomeVerified', 'notes', 'status']);
        $this->status = 'pending';
    }
    
    public function editScreening()
    {
        if (!$this->screening) return;
        
        $this->showForm = true;
        $this->isEditing = true;
        $this->fillFormFromScreening();
    }
    
    public function cancelForm()
    {
        $this->showForm = false;
    }
    
    public function saveScreening()
    {
        $this->validate();
        
        if ($this->isEditing && $this->screening) {
            // Update existing screening
            $this->screening->update([
                'credit_check_passed' => $this->creditCheckPassed,
                'background_check_passed' => $this->backgroundCheckPassed,
                'eviction_check_passed' => $this->evictionCheckPassed,
                'employment_verified' => $this->employmentVerified,
                'income_verified' => $this->incomeVerified,
                'notes' => $this->notes,
                'status' => $this->status,
                'completed_at' => $this->status === 'completed' ? now() : $this->screening->completed_at
            ]);
        } else {
            // Create new screening
            $this->screening = TenantScreening::create([
                'tenant_id' => $this->tenantId,
                'landlord_id' => $this->role === 'landlord' ? $this->user->id : $this->unit->property->user_id,
                'unit_id' => $this->unitId,
                'credit_check_passed' => $this->creditCheckPassed,
                'background_check_passed' => $this->backgroundCheckPassed,
                'eviction_check_passed' => $this->evictionCheckPassed,
                'employment_verified' => $this->employmentVerified,
                'income_verified' => $this->incomeVerified,
                'notes' => $this->notes,
                'status' => $this->status,
                'completed_at' => $this->status === 'completed' ? now() : null
            ]);
        }
        
        $this->showForm = false;
        $this->dispatch('screening-updated');
        $this->dispatch('notify', [
            'message' => 'Tenant screening ' . ($this->isEditing ? 'updated' : 'created') . ' successfully',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        return view('livewire.onboarding.tenant-screening');
    }
}
