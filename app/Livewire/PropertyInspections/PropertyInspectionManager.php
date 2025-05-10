<?php

namespace App\Livewire\PropertyInspections;

use App\Models\PropertyInspection;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class PropertyInspectionManager extends Component
{
    use WithPagination;
    
    public $selectedInspection = null;
    public $selectedInspectionId = null;
    public $activeTab = 'list'; // list, schedule, report, photos
    public $search = '';
    public $filterStatus = '';
    public $filterType = '';
    public $dateRange = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterType' => ['except' => ''],
        'dateRange' => ['except' => ''],
    ];
    
    protected $listeners = [
        'inspectionScheduled' => 'handleInspectionScheduled',
        'inspectionUpdated' => 'handleInspectionUpdated'
    ];
    
    public function handleInspectionScheduled()
    {
        $this->activeTab = 'list';
        $this->resetPage();
    }
    
    public function handleInspectionUpdated()
    {
        // Refresh the current inspection if we're viewing one
        if ($this->selectedInspectionId) {
            $this->selectInspection($this->selectedInspectionId);
        }
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    
    public function updatingFilterType()
    {
        $this->resetPage();
    }
    
    public function updatingDateRange()
    {
        $this->resetPage();
    }
    
    public function selectInspection($id)
    {
        $this->selectedInspectionId = $id;
        $this->selectedInspection = PropertyInspection::findOrFail($id);
        $this->activeTab = 'report';
    }
    
    public function newInspection()
    {
        $this->selectedInspection = null;
        $this->selectedInspectionId = null;
        $this->activeTab = 'schedule';
    }
    
    public function showPhotos($id)
    {
        $this->selectedInspectionId = $id;
        $this->selectedInspection = PropertyInspection::findOrFail($id);
        $this->activeTab = 'photos';
    }
    
    public function showReport($id)
    {
        $this->selectedInspectionId = $id;
        $this->selectedInspection = PropertyInspection::findOrFail($id);
        $this->activeTab = 'report';
    }
    
    public function render()
    {
        // Build query
        $query = PropertyInspection::query()
            ->with(['unit', 'tenant', 'landlord'])
            ->when($this->search, function ($query) {
                return $query->whereHas('unit', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('tenant', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                return $query->where('status', $this->filterStatus);
            })
            ->when($this->filterType, function ($query) {
                return $query->where('type', $this->filterType);
            })
            ->when($this->dateRange, function ($query) {
                // Handle date range filtering
                if ($this->dateRange === 'upcoming') {
                    return $query->where('inspection_date', '>=', now()->format('Y-m-d'));
                } elseif ($this->dateRange === 'past') {
                    return $query->where('inspection_date', '<', now()->format('Y-m-d'));
                } elseif ($this->dateRange === 'this_week') {
                    return $query->whereBetween('inspection_date', [
                        now()->startOfWeek()->format('Y-m-d'),
                        now()->endOfWeek()->format('Y-m-d')
                    ]);
                } elseif ($this->dateRange === 'this_month') {
                    return $query->whereBetween('inspection_date', [
                        now()->startOfMonth()->format('Y-m-d'),
                        now()->endOfMonth()->format('Y-m-d')
                    ]);
                }
            });
            
        // Filter based on user role
        if (Auth::user()->hasRole('tenant')) {
            $query->where('tenant_id', Auth::id());
        } elseif (Auth::user()->hasRole('landlord')) {
            $query->where('landlord_id', Auth::id());
        }
        
        // Get paginated results
        $inspections = $query->latest()->paginate(10);
        
        return view('livewire.property-inspections.property-inspection-manager', [
            'inspections' => $inspections
        ]);
    }
}
