<?php

namespace App\Livewire\PropertyRenovations;

use App\Models\Property;
use App\Models\PropertyRenovation;
use App\Models\Unit;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class RenovationManager extends Component
{
    use WithPagination;
    
    public $activeTab = 'list'; // list, create, details, vendors, expenses
    public $selectedRenovationId = null;
    public $selectedRenovation = null;
    
    // Filters
    public $search = '';
    public $statusFilter = '';
    public $propertyFilter = '';
    public $dateRange = '';
    public $properties = [];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'propertyFilter' => ['except' => ''],
        'dateRange' => ['except' => ''],
    ];
    
    protected $listeners = [
        'renovationCreated' => 'handleRenovationCreated',
        'renovationUpdated' => 'handleRenovationUpdated',
        'vendorAdded' => 'refreshRenovationDetails',
        'expenseAdded' => 'refreshRenovationDetails',
        'selectTab' => 'setActiveTab',
    ];
    
    public function mount()
    {
        $user = Auth::user();
        
        // Load properties based on user role
        if ($user->hasRole('Developer') || $user->hasRole('Admin')) {
            $this->properties = Property::all();
        } elseif ($user->hasRole('Landlord')) {
            $this->properties = $user->properties;
        }
    }
    
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function handleRenovationCreated($renovationId)
    {
        $this->selectRenovation($renovationId);
        $this->activeTab = 'details';
    }
    
    public function handleRenovationUpdated()
    {
        if ($this->selectedRenovationId) {
            $this->refreshRenovationDetails();
        }
    }
    
    public function refreshRenovationDetails()
    {
        if ($this->selectedRenovationId) {
            $this->selectedRenovation = PropertyRenovation::with(['property', 'unit', 'vendors', 'expenses'])
                ->findOrFail($this->selectedRenovationId);
        }
    }
    
    public function selectRenovation($id)
    {
        $this->selectedRenovationId = $id;
        $this->refreshRenovationDetails();
        $this->activeTab = 'details';
    }
    
    public function showCreateForm()
    {
        $this->selectedRenovationId = null;
        $this->selectedRenovation = null;
        $this->activeTab = 'create';
    }
    
    public function showVendors($renovationId = null)
    {
        if ($renovationId) {
            $this->selectRenovation($renovationId);
        }
        
        if ($this->selectedRenovationId) {
            $this->activeTab = 'vendors';
        }
    }
    
    public function showExpenses($renovationId = null)
    {
        if ($renovationId) {
            $this->selectRenovation($renovationId);
        }
        
        if ($this->selectedRenovationId) {
            $this->activeTab = 'expenses';
        }
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    
    public function updatingPropertyFilter()
    {
        $this->resetPage();
    }
    
    public function updatingDateRange()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $user = Auth::user();
        
        // Build query
        $query = PropertyRenovation::with(['property', 'unit', 'vendors']);
        
        // Filter based on user role
        if ($user->hasRole('Landlord')) {
            $propertyIds = $user->properties->pluck('id')->toArray();
            $query->whereIn('property_id', $propertyIds);
        }
        
        // Apply filters
        $query->when($this->search, function($query) {
            return $query->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhereHas('property', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
        });
        
        $query->when($this->statusFilter, function($query) {
            return $query->where('status', $this->statusFilter);
        });
        
        $query->when($this->propertyFilter, function($query) {
            return $query->where('property_id', $this->propertyFilter);
        });
        
        $query->when($this->dateRange, function($query) {
            if ($this->dateRange === 'current') {
                return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
            } elseif ($this->dateRange === 'upcoming') {
                return $query->where('start_date', '>', now());
            } elseif ($this->dateRange === 'completed') {
                return $query->where('status', 'completed');
            }
        });
        
        // Get paginated results
        $renovations = $query->latest()->paginate(10);
        
        return view('livewire.property-renovations.renovation-manager', [
            'renovations' => $renovations,
            'statusOptions' => [
                'planned' => 'Planned',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
        ]);
    }
}
