<?php

namespace App\Livewire\Units;

use Livewire\Component;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class Manage extends Component
{
    public $properties;
    public $units;
    public $property_id;
    public $unit_number;
    public $rent;
    public $manual_water = false;
    public $manual_electricity = false;
    public $status = 'vacant';
    public $unit_id;

    public function mount()
    {
        $this->properties = Auth::user()->properties;
        $this->loadUnits();
    }

    public function loadUnits()
    {
        $propertyIds = $this->properties->pluck('id');
        $this->units = Unit::whereIn('property_id', $propertyIds)->with('property')->get();
    }

    public function create()
    {
        $data = $this->validate([
            'property_id' => 'required|exists:properties,id',
            'unit_number' => 'required|string',
            'rent' => 'required|numeric|min:0',
            'status' => 'required|in:vacant,occupied,maintenance',
            'manual_water' => 'boolean',
            'manual_electricity' => 'boolean',
        ]);

        Unit::create($data);
        $this->resetInputFields();
        $this->loadUnits();
        session()->flash('status', 'Unit added successfully.');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $this->unit_id = $unit->id;
        $this->property_id = $unit->property_id;
        $this->unit_number = $unit->unit_number;
        $this->rent = $unit->rent;
        $this->status = $unit->status;
        $this->manual_water = $unit->manual_water;
        $this->manual_electricity = $unit->manual_electricity;
    }

    public function update()
    {
        $data = $this->validate([
            'property_id' => 'required|exists:properties,id',
            'unit_number' => 'required|string',
            'rent' => 'required|numeric|min:0',
            'status' => 'required|in:vacant,occupied,maintenance',
            'manual_water' => 'boolean',
            'manual_electricity' => 'boolean',
        ]);

        Unit::findOrFail($this->unit_id)->update($data);
        $this->resetInputFields();
        $this->loadUnits();
        session()->flash('status', 'Unit updated successfully.');
    }

    public function delete($id)
    {
        Unit::findOrFail($id)->delete();
        $this->loadUnits();
        session()->flash('status', 'Unit deleted successfully.');
    }

    private function resetInputFields()
    {
        $this->unit_id = null;
        $this->property_id = '';
        $this->unit_number = '';
        $this->rent = '';
        $this->status = 'vacant';
        $this->manual_water = false;
        $this->manual_electricity = false;
    }

    public function render()
    {
        return view('livewire.units.manage');
    }
}
