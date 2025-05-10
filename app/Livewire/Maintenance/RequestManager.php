<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\MaintenanceRequest;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class RequestManager extends Component
{
    public $title;
    public $description;
    public $unit_id;
    public $status = 'pending';
    public $budget_estimate;
    public $admin_notes;
    public $maintenance_request_id;
    public $units;
    public $requests;
    public $is_landlord;

    public function mount()
    {
        $user = Auth::user();
        $this->is_landlord = $user->hasRole('Landlord');
        
        if ($this->is_landlord) {
            // Landlord sees requests for their properties
            $propertyIds = $user->properties->pluck('id')->toArray();
            $this->units = Unit::whereIn('property_id', $propertyIds)->get();
            $this->requests = MaintenanceRequest::whereIn('unit_id', $this->units->pluck('id'))
                ->with(['user', 'unit.property'])
                ->latest()
                ->get();
        } else {
            // Tenant sees only their requests
            $this->units = Unit::whereHas('invoices', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();
            $this->requests = $user->maintenanceRequests()
                ->with(['unit.property'])
                ->latest()
                ->get();
        }
    }
    
    public function create()
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_id' => 'required|exists:units,id',
        ]);
        
        Auth::user()->maintenanceRequests()->create($data);
        $this->resetInput();
        $this->mount(); // Refresh the list
        session()->flash('status', 'Maintenance request submitted successfully');
    }
    
    public function edit($id)
    {
        $request = MaintenanceRequest::findOrFail($id);
        $this->maintenance_request_id = $request->id;
        $this->title = $request->title;
        $this->description = $request->description;
        $this->unit_id = $request->unit_id;
        $this->status = $request->status;
        $this->budget_estimate = $request->budget_estimate;
        $this->admin_notes = $request->admin_notes;
    }
    
    public function update()
    {
        $request = MaintenanceRequest::findOrFail($this->maintenance_request_id);
        
        // Role-based permission check
        $data = [];
        if ($this->is_landlord) {
            $data = $this->validate([
                'status' => 'required|in:pending,in_progress,completed',
                'budget_estimate' => 'nullable|numeric|min:0',
                'admin_notes' => 'nullable|string'
            ]);
        } else {
            $data = $this->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);
        }
        
        $request->update($data);
        $this->resetInput();
        $this->mount(); // Refresh the list
        session()->flash('status', 'Maintenance request updated successfully');
    }
    
    public function delete($id)
    {
        $request = MaintenanceRequest::findOrFail($id);
        // Only allow deletion if pending and by the creator or a landlord
        if ($request->status === 'pending' && ($request->user_id === Auth::id() || $this->is_landlord)) {
            $request->delete();
            session()->flash('status', 'Maintenance request deleted successfully');
            $this->mount(); // Refresh the list
        } else {
            session()->flash('error', 'Cannot delete this maintenance request');
        }
    }
    
    private function resetInput()
    {
        $this->maintenance_request_id = null;
        $this->title = '';
        $this->description = '';
        $this->unit_id = '';
        $this->status = 'pending';
        $this->budget_estimate = null;
        $this->admin_notes = '';
    }

    public function render()
    {
        return view('livewire.maintenance.request-manager');
    }
}
