<?php

namespace App\Livewire\PropertyRenovations;

use App\Models\PropertyRenovation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class RenovationDetails extends Component
{
    use WithFileUploads;
    
    public $renovation;
    public $renovationId;
    public $isEditing = false;
    
    // Editable fields
    public $title;
    public $description;
    public $start_date;
    public $end_date;
    public $budget;
    public $status;
    public $notes;
    public $documents = [];
    public $existingDocuments = [];
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'budget' => 'required|numeric|min:0',
        'status' => 'required|in:planned,in_progress,completed,cancelled',
        'notes' => 'nullable|string',
        'documents.*' => 'nullable|file|max:10240', // 10MB max per file
    ];
    
    public function mount($renovationId = null)
    {
        if ($renovationId) {
            $this->renovationId = $renovationId;
            $this->loadRenovation();
        }
    }
    
    public function loadRenovation()
    {
        $this->renovation = PropertyRenovation::with(['property', 'unit', 'vendors', 'expenses.vendor'])
            ->findOrFail($this->renovationId);
        
        // Verify authorization
        if (!$this->authorizeAccess()) {
            session()->flash('error', 'You do not have permission to view this renovation project.');
            return redirect()->route('dashboard');
        }
        
        $this->loadRenovationData();
    }
    
    private function authorizeAccess()
    {
        $user = Auth::user();
        
        // Check if user is admin or landlord of this property
        return $user->hasRole(['Developer', 'Admin']) ||
                ($user->hasRole('Landlord') && $user->properties->contains('id', $this->renovation->property_id));
    }
    
    private function loadRenovationData()
    {
        $this->title = $this->renovation->title;
        $this->description = $this->renovation->description;
        $this->start_date = $this->renovation->start_date->format('Y-m-d');
        $this->end_date = $this->renovation->end_date->format('Y-m-d');
        $this->budget = $this->renovation->budget;
        $this->status = $this->renovation->status;
        $this->notes = $this->renovation->notes;
        $this->existingDocuments = $this->renovation->document_paths ?? [];
    }
    
    public function enableEditing()
    {
        $this->isEditing = true;
    }
    
    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->loadRenovationData(); // Reset to original values
        $this->documents = [];
    }
    
    public function updateRenovation()
    {
        $this->validate();
        
        $updates = [
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'budget' => $this->budget,
            'status' => $this->status,
            'notes' => $this->notes,
        ];
        
        // Handle document uploads
        if (!empty($this->documents)) {
            $documentPaths = $this->existingDocuments ?: [];
            
            foreach ($this->documents as $document) {
                $path = $document->store('public/renovations/' . $this->renovationId . '/documents');
                $documentPaths[] = [
                    'path' => str_replace('public/', '', $path),
                    'name' => $document->getClientOriginalName(),
                    'uploaded_at' => now()->toDateTimeString(),
                    'uploaded_by' => Auth::id(),
                ];
            }
            
            $updates['document_paths'] = $documentPaths;
        }
        
        $this->renovation->update($updates);
        $this->renovation->refresh();
        
        $this->documents = [];
        $this->isEditing = false;
        $this->loadRenovationData();
        
        session()->flash('message', 'Renovation details updated successfully.');
        
        // Emit event to parent component
        $this->dispatch('renovationUpdated');
    }
    
    public function removeDocument($index)
    {
        if (isset($this->existingDocuments[$index])) {
            // Remove from storage if needed
            $path = 'public/' . $this->existingDocuments[$index]['path'];
            if (\Storage::exists($path)) {
                \Storage::delete($path);
            }
            
            // Remove from array
            unset($this->existingDocuments[$index]);
            $this->existingDocuments = array_values($this->existingDocuments);
            
            // Update renovation record
            $this->renovation->update([
                'document_paths' => $this->existingDocuments
            ]);
            
            session()->flash('message', 'Document removed successfully.');
        }
    }
    
    public function render()
    {
        return view('livewire.property-renovations.renovation-details', [
            'statusOptions' => [
                'planned' => 'Planned',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
        ]);
    }
}
