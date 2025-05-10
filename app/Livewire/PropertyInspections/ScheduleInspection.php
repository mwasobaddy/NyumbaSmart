<?php

namespace App\Livewire\PropertyInspections;

use App\Models\PropertyInspection;
use App\Models\Unit;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ScheduleInspection extends Component
{
    public $units = [];
    public $tenants = [];
    
    // Form fields
    public $unit_id;
    public $tenant_id;
    public $type = 'routine';
    public $inspection_date;
    public $notes;
    
    // Available inspection types
    public $inspectionTypes = [
        'move_in' => 'Move-in Inspection',
        'move_out' => 'Move-out Inspection',
        'routine' => 'Routine Inspection',
        'maintenance' => 'Maintenance Inspection'
    ];
    
    public function mount()
    {
        // Load units that the current user has access to
        if (Auth::user()->hasRole('landlord')) {
            $this->units = Unit::where('landlord_id', Auth::id())->get();
        } elseif (Auth::user()->hasRole('admin')) {
            $this->units = Unit::all();
        }
        
        // Set default date to tomorrow
        $this->inspection_date = now()->addDay()->format('Y-m-d');
    }
    
    public function updatedUnitId($value)
    {
        if (!$value) {
            $this->tenants = [];
            return;
        }
        
        // Load tenants for the selected unit
        $unit = Unit::find($value);
        if ($unit && $unit->tenant_id) {
            $this->tenants = [$unit->tenant_id => User::find($unit->tenant_id)];
            $this->tenant_id = $unit->tenant_id;
        } else {
            $this->tenants = [];
        }
    }
    
    public function schedule()
    {
        $this->validate([
            'unit_id' => 'required|exists:units,id',
            'tenant_id' => 'required|exists:users,id',
            'type' => 'required|in:move_in,move_out,routine,maintenance',
            'inspection_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);
        
        // Create a default checklist based on inspection type
        $checklist = $this->generateDefaultChecklist($this->type);
        
        // Create the inspection
        PropertyInspection::create([
            'unit_id' => $this->unit_id,
            'tenant_id' => $this->tenant_id,
            'landlord_id' => Auth::id(),
            'type' => $this->type,
            'inspection_date' => $this->inspection_date,
            'status' => 'scheduled',
            'checklist_items' => $checklist,
            'notes' => $this->notes,
        ]);
        
        // Reset the form
        $this->reset(['unit_id', 'tenant_id', 'type', 'notes']);
        $this->inspection_date = now()->addDay()->format('Y-m-d');
        
        // Show success message
        session()->flash('message', 'Inspection scheduled successfully.');
        
        // Emit event for parent components
        $this->dispatch('inspectionScheduled');
    }
    
    private function generateDefaultChecklist($type)
    {
        // Generate a default checklist based on inspection type
        $commonItems = [
            ['name' => 'Walls', 'condition' => '', 'notes' => ''],
            ['name' => 'Floors', 'condition' => '', 'notes' => ''],
            ['name' => 'Ceiling', 'condition' => '', 'notes' => ''],
            ['name' => 'Windows', 'condition' => '', 'notes' => ''],
            ['name' => 'Doors', 'condition' => '', 'notes' => ''],
            ['name' => 'Light fixtures', 'condition' => '', 'notes' => ''],
            ['name' => 'Electrical outlets', 'condition' => '', 'notes' => ''],
            ['name' => 'Bathroom fixtures', 'condition' => '', 'notes' => ''],
            ['name' => 'Kitchen appliances', 'condition' => '', 'notes' => ''],
        ];
        
        // Add specific items based on inspection type
        if ($type === 'move_in' || $type === 'move_out') {
            $commonItems[] = ['name' => 'Keys handed over', 'condition' => '', 'notes' => ''];
            $commonItems[] = ['name' => 'Utility readings', 'condition' => '', 'notes' => ''];
        }
        
        if ($type === 'maintenance') {
            $commonItems[] = ['name' => 'Specific maintenance issue', 'condition' => '', 'notes' => ''];
        }
        
        return $commonItems;
    }
    
    public function render()
    {
        return view('livewire.property-inspections.schedule-inspection');
    }
}
