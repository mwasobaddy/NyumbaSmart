<?php

namespace App\Livewire\PropertyInspections;

use App\Models\PropertyInspection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoDocumentation extends Component
{
    use WithFileUploads;

    public $inspection;
    public $inspectionId;
    public $photos = [];
    public $existingPhotos = [];
    public $photoDescriptions = [];
    
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
        
        // Load existing photos
        $this->loadExistingPhotos();
    }
    
    private function authorizeAccess()
    {
        $user = Auth::user();
        
        // Check if user is admin, landlord of this property, or tenant of this unit
        return $user->hasRole('admin') ||
               $user->id === $this->inspection->landlord_id ||
               $user->id === $this->inspection->tenant_id;
    }
    
    private function loadExistingPhotos()
    {
        $this->existingPhotos = [];
        $this->photoDescriptions = [];
        
        if ($this->inspection->image_paths) {
            $this->existingPhotos = $this->inspection->image_paths;
            
            // Extract descriptions
            foreach ($this->existingPhotos as $photo) {
                if (isset($photo['path']) && isset($photo['description'])) {
                    $this->photoDescriptions[$photo['path']] = $photo['description'];
                }
            }
        }
    }
    
    public function savePhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:5120', // 5MB max per image
        ]);
        
        if (!count($this->photos)) {
            session()->flash('info', 'No photos selected for upload.');
            return;
        }
        
        $uploadedPhotos = [];
        $existingPhotos = $this->inspection->image_paths ?: [];
        
        // Upload new photos
        foreach ($this->photos as $photo) {
            // Generate a unique filename
            $filename = 'inspections/' . $this->inspection->id . '/' . time() . '-' . $photo->getClientOriginalName();
            
            // Store the photo
            $path = $photo->storeAs('public', $filename);
            
            // Add to uploaded photos
            $uploadedPhotos[] = [
                'path' => $filename,
                'description' => '',
                'uploaded_at' => now()->toDateTimeString(),
                'uploaded_by' => Auth::id(),
            ];
        }
        
        // Merge with existing photos
        $allPhotos = array_merge($existingPhotos, $uploadedPhotos);
        
        // Update the inspection record
        $this->inspection->update([
            'image_paths' => $allPhotos
        ]);
        
        // Reset the upload form
        $this->photos = [];
        
        // Reload existing photos
        $this->loadExistingPhotos();
        
        session()->flash('message', count($uploadedPhotos) . ' photos uploaded successfully.');
        
        // Emit event for parent components
        $this->dispatch('inspectionUpdated');
    }
    
    public function updateDescription($index, $description)
    {
        if (isset($this->existingPhotos[$index])) {
            $this->existingPhotos[$index]['description'] = $description;
            
            // Update the inspection
            $this->inspection->update([
                'image_paths' => $this->existingPhotos
            ]);
            
            session()->flash('message', 'Photo description updated.');
        }
    }
    
    public function removePhoto($index)
    {
        if (isset($this->existingPhotos[$index])) {
            $photoToRemove = $this->existingPhotos[$index];
            
            // Delete the file from storage if it exists
            if (isset($photoToRemove['path']) && Storage::exists('public/' . $photoToRemove['path'])) {
                Storage::delete('public/' . $photoToRemove['path']);
            }
            
            // Remove from array
            unset($this->existingPhotos[$index]);
            $this->existingPhotos = array_values($this->existingPhotos);
            
            // Update the inspection
            $this->inspection->update([
                'image_paths' => $this->existingPhotos
            ]);
            
            session()->flash('message', 'Photo removed successfully.');
            
            // Reload existing photos
            $this->loadExistingPhotos();
        }
    }
    
    public function render()
    {
        return view('livewire.property-inspections.photo-documentation');
    }
}
