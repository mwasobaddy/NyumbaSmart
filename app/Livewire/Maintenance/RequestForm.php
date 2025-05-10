<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\MaintenanceRequest;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class RequestForm extends Component
{
    public $units;
    public $unit_id;
    public $title;
    public $description;
    public $maintenance_id;

    public function mount()
    {
        // For tenants: show only units they're associated with
        // For landlords: show all units they own
        if (Auth::user()->hasRole('Tenant')) {
            $this->units = Unit::where('status', 'occupied')
                ->whereHas('invoices', function($query) {
                    $query->where('user_id', Auth::id());
                })->get();
        } else {
            $propertyIds = Auth::user()->properties->pluck('id');
            $this->units = Unit::whereIn('property_id', $propertyIds)->get();
        }
    }

    public function submit()
    {
        $data = $this->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|min:5|max:100',
            'description' => 'required|min:10',
        ]);

        $data['user_id'] = Auth::id();

        MaintenanceRequest::create($data);

        $this->reset(['unit_id', 'title', 'description']);
        session()->flash('status', 'Maintenance request submitted successfully.');
    }

    public function render()
    {
        return view('livewire.maintenance.request-form');
    }
}
