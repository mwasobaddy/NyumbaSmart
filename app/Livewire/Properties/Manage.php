<?php

namespace App\Livewire\Properties;

use Livewire\Component;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class Manage extends Component
{
    public $properties;
    public $name;
    public $address;
    public $description;
    public $logo_url;
    public $theme_color;
    public $app_name;
    public $property_id;

    public function mount()
    {
        $this->properties = Auth::user()->properties;
    }

    public function create()
    {
        $data = $this->validate([
            'name' => 'required',
            'address' => 'required',
            'description' => 'nullable',
            'logo_url' => 'nullable',
            'theme_color' => 'nullable',
            'app_name' => 'nullable',
        ]);

        Auth::user()->properties()->create($data);
        $this->resetInputFields();
        $this->properties = Auth::user()->properties;
        session()->flash('status', 'Property created successfully.');
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $this->property_id = $property->id;
        $this->name = $property->name;
        $this->address = $property->address;
        $this->description = $property->description;
        $this->logo_url = $property->logo_url;
        $this->theme_color = $property->theme_color;
        $this->app_name = $property->app_name;
    }

    public function update()
    {
        $data = $this->validate([
            'name' => 'required',
            'address' => 'required',
            'description' => 'nullable',
            'logo_url' => 'nullable',
            'theme_color' => 'nullable',
            'app_name' => 'nullable',
        ]);

        $property = Property::findOrFail($this->property_id);
        $property->update($data);
        $this->resetInputFields();
        $this->properties = Auth::user()->properties;
        session()->flash('status', 'Property updated successfully.');
    }

    public function delete($id)
    {
        Property::findOrFail($id)->delete();
        $this->properties = Auth::user()->properties;
        session()->flash('status', 'Property deleted successfully.');
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->address = '';
        $this->description = '';
        $this->logo_url = '';
        $this->theme_color = '';
        $this->app_name = '';
        $this->property_id = null;
    }

    public function render()
    {
        return view('livewire.properties.manage');
    }
}
