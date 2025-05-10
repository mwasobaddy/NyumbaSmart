<?php

namespace App\Livewire\PropertyInspections;

use App\Models\PropertyInspection;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class InspectionReport extends Component
{
    use WithFileUploads;

    public $inspection;
    public $inspectionId;
    public $unitName;
    
    // Form data
    public $checklist_items = [];
    public $overall_condition;
    public $notes;
    public $status = 'in_progress';
    
    // Condition options
    public $conditionOptions = [
        'excellent' => 'Excellent',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
        'needs_repair' => 'Needs Repair'
    ];
    
    public function mount($inspectionId = null)
    {
        if ($inspectionId) {
            $this->inspectionId = $inspectionId;
            $this->loadInspection();
        }
    }
    
    public function loadInspection()
    {
        $this->inspection = PropertyInspection::findOrFail($this->inspectionId);
        
        // Verify authorization
        if (!$this->authorizeAccess()) {
            session()->flash('error', 'You do not have permission to view this inspection.');
            return redirect()->route('dashboard');
        }
        
        // Load inspection data
        $this->checklist_items = $this->inspection->checklist_items;
        $this->overall_condition = $this->inspection->overall_condition;
        $this->notes = $this->inspection->notes;
        $this->status = $this->inspection->status;
        
        // Get unit name for display
        $unit = Unit::find($this->inspection->unit_id);
        $this->unitName = $unit ? $unit->name : 'Unknown Unit';
    }
    
    private function authorizeAccess()
    {
        $user = Auth::user();
        
        // Check if user is admin, landlord of this property, or tenant of this unit
        return $user->hasRole('admin') ||
               $user->id === $this->inspection->landlord_id ||
               $user->id === $this->inspection->tenant_id;
    }
    
    public function updateChecklistItem($index, $field, $value)
    {
        $this->checklist_items[$index][$field] = $value;
    }
    
    public function addChecklistItem()
    {
        $this->checklist_items[] = [
            'name' => '',
            'condition' => '',
            'notes' => ''
        ];
    }
    
    public function removeChecklistItem($index)
    {
        unset($this->checklist_items[$index]);
        $this->checklist_items = array_values($this->checklist_items);
    }
    
    public function saveReport()
    {
        // Validate input
        $this->validate([
            'overall_condition' => 'required|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed',
        ]);
        
        // Update inspection
        $this->inspection->update([
            'checklist_items' => $this->checklist_items,
            'overall_condition' => $this->overall_condition,
            'notes' => $this->notes,
            'status' => $this->status,
        ]);
        
        session()->flash('message', 'Inspection report saved successfully.');
        
        // Emit event for parent components
        $this->dispatch('inspectionUpdated');
    }
    
    public function completeInspection()
    {
        // Mark as completed only if checklist items are filled out
        $validItems = true;
        foreach ($this->checklist_items as $item) {
            if (empty($item['name']) || empty($item['condition'])) {
                $validItems = false;
                break;
            }
        }
        
        if (!$validItems) {
            session()->flash('error', 'Please fill out all checklist items before completing the inspection.');
            return;
        }
        
        $this->status = 'completed';
        $this->saveReport();
    }
    
    public function signInspection()
    {
        $user = Auth::user();
        
        // Check if user is tenant or landlord of this inspection
        if ($user->id === $this->inspection->tenant_id) {
            $this->inspection->update([
                'tenant_signed' => true,
                'tenant_signed_at' => now(),
            ]);
            session()->flash('message', 'Inspection signed as tenant.');
        } elseif ($user->id === $this->inspection->landlord_id) {
            $this->inspection->update([
                'landlord_signed' => true,
                'landlord_signed_at' => now(),
            ]);
            session()->flash('message', 'Inspection signed as landlord.');
        } else {
            session()->flash('error', 'You are not authorized to sign this inspection.');
        }
        
        $this->loadInspection();
    }
    
    public function render()
    {
        return view('livewire.property-inspections.inspection-report');
    }
}
