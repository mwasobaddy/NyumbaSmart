<?php

namespace App\Livewire\Reviews;

use Livewire\Component;
use App\Models\Review;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class Manager extends Component
{
    public $properties;
    public $units;
    public $reviews;
    public $unit_id;
    public $rating = 5;
    public $comment;
    public $review_id;
    public $is_landlord;
    public $property_id; // For filtering

    public function mount()
    {
        $user = Auth::user();
        $this->is_landlord = $user->hasRole('Landlord');
        
        if ($this->is_landlord) {
            // Landlord sees reviews for their properties
            $this->properties = $user->properties;
            $propertyIds = $this->properties->pluck('id')->toArray();
            $this->units = Unit::whereIn('property_id', $propertyIds)->get();
            $this->loadReviews();
        } else {
            // Tenant sees properties they've rented and their own reviews
            $this->units = Unit::whereHas('invoices', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with('property')->get();
            
            $this->properties = Property::whereIn('id', $this->units->pluck('property_id')->unique())->get();
            $this->reviews = $user->reviews()->with(['unit.property'])->latest()->get();
        }
    }
    
    public function loadReviews()
    {
        if ($this->is_landlord) {
            $query = Review::whereIn('unit_id', $this->units->pluck('id'))
                ->with(['user', 'unit.property'])
                ->latest();
                
            if ($this->property_id) {
                $propertyUnitIds = Unit::where('property_id', $this->property_id)->pluck('id');
                $query->whereIn('unit_id', $propertyUnitIds);
            }
            
            $this->reviews = $query->get();
        }
    }
    
    public function filterByProperty()
    {
        $this->loadReviews();
    }
    
    public function create()
    {
        $data = $this->validate([
            'unit_id' => 'required|exists:units,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);
        
        // Check if user already reviewed this unit
        $existingReview = Auth::user()->reviews()->where('unit_id', $this->unit_id)->first();
        
        if ($existingReview) {
            session()->flash('error', 'You have already reviewed this unit. You can edit your existing review instead.');
            return;
        }
        
        Auth::user()->reviews()->create($data);
        $this->resetInput();
        $this->mount(); // Refresh the lists
        session()->flash('status', 'Review submitted successfully. Thank you for your feedback!');
    }
    
    public function edit($id)
    {
        $review = Review::findOrFail($id);
        
        // Only the author can edit their own review
        if ($review->user_id !== Auth::id()) {
            session()->flash('error', 'You cannot edit reviews submitted by others.');
            return;
        }
        
        $this->review_id = $review->id;
        $this->unit_id = $review->unit_id;
        $this->rating = $review->rating;
        $this->comment = $review->comment;
    }
    
    public function update()
    {
        $data = $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);
        
        $review = Review::findOrFail($this->review_id);
        
        // Only the author can update their own review
        if ($review->user_id !== Auth::id()) {
            session()->flash('error', 'You cannot update reviews submitted by others.');
            return;
        }
        
        $review->update($data);
        $this->resetInput();
        $this->mount(); // Refresh the lists
        session()->flash('status', 'Review updated successfully.');
    }
    
    public function delete($id)
    {
        $review = Review::findOrFail($id);
        
        // Only the author can delete their own review
        if ($review->user_id !== Auth::id()) {
            session()->flash('error', 'You cannot delete reviews submitted by others.');
            return;
        }
        
        $review->delete();
        $this->mount(); // Refresh the lists
        session()->flash('status', 'Review deleted successfully.');
    }
    
    private function resetInput()
    {
        $this->review_id = null;
        $this->unit_id = '';
        $this->rating = 5;
        $this->comment = '';
    }

    public function render()
    {
        return view('livewire.reviews.manager');
    }
}
