<?php

namespace App\Livewire\PropertyRenovations;

use App\Models\Property;
use App\Models\PropertyRenovation;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateRenovation extends Component
{
    public $property_id;
    public $unit_id = null;
    public $title;
    public $description;
    public $start_date;
    public $end_date;
    public $budget;
    public $notes;
    
    public $properties = [];
    public $units = [];
    
    protected $rules = [
        'property_id' => 'required|exists:properties,id',
        'unit_id' => 'nullable|exists:units,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'budget' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
    ];
    
    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(30)->format('Y-m-d');
        
        $user = Auth::user();
        
        // Load properties based on user role
        if ($user->hasRole('Developer') || $user->hasRole('Admin')) {
            $this->properties = Property::all();
        } elseif ($user->hasRole('Landlord')) {
            $this->properties = $user->properties;
            
            // If landlord has only one property, preselect it
            if ($this->properties->count() === 1) {
                $this->property_id = $this->properties->first()->id;
                $this->updatedPropertyId();
            }
        }
    }
    
    public function updatedPropertyId()
    {
        $this->units = Property::find($this->property_id)?->units ?? [];
        $this->unit_id = null;
    }
    
    public function createRenovation()
    {
        $this->validate();
        
        // Create the renovation
        $renovation = PropertyRenovation::create([
            'property_id' => $this->property_id,
            'unit_id' => $this->unit_id,
            'user_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'actual_cost' => 0, // Initially zero
            'status' => 'planned',
            'notes' => $this->notes,
        ]);
        
        // Reset form
        $this->reset(['title', 'description', 'budget', 'notes', 'unit_id']);
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(30)->format('Y-m-d');
        
        session()->flash('message', 'Renovation project created successfully.');
        
        // Emit event to parent component
        $this->dispatch('renovationCreated', $renovation->id);
    }
    
    public function render()
    {
        return view('livewire.property-renovations.create-renovation');
    }
}
