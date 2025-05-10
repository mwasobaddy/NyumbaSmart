<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\VacateNotice;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Main extends Component
{
    public $user;
    public $role;
    public $stats = [];
    public $recentInvoices = [];
    public $recentMaintenance = [];
    public $pendingNotices = [];
    public $expiringSubscription;
    public $activePlan;
    public $recommendation;

    public function mount()
    {
        $this->user = Auth::user();
        
        // Determine primary role for dashboard
        if ($this->user->hasRole('Developer')) {
            $this->role = 'admin';
            $this->loadAdminDashboard();
        } elseif ($this->user->hasRole('Landlord')) {
            $this->role = 'landlord';
            $this->loadLandlordDashboard();
        } elseif ($this->user->hasRole('Tenant')) {
            $this->role = 'tenant';
            $this->loadTenantDashboard();
        } else {
            $this->role = 'guest';
        }
    }
    
    private function loadAdminDashboard()
    {
        $this->stats = [
            'total_properties' => Property::count(),
            'total_units' => Unit::count(),
            'total_landlords' => \App\Models\User::role('Landlord')->count(),
            'total_tenants' => \App\Models\User::role('Tenant')->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];
        
        $this->recentInvoices = Invoice::with(['user', 'unit.property'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }
    
    private function loadLandlordDashboard()
    {
        $propertyIds = $this->user->properties->pluck('id')->toArray();
        $unitIds = Unit::whereIn('property_id', $propertyIds)->pluck('id')->toArray();
        
        // Calculate occupancy rate
        $totalUnits = count($unitIds);
        $occupiedUnits = Unit::whereIn('id', $unitIds)->where('status', 'occupied')->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;
        
        // Calculate collection rate
        $monthStart = Carbon::now()->startOfMonth();
        $totalInvoiced = Invoice::whereIn('unit_id', $unitIds)
            ->where('created_at', '>=', $monthStart)
            ->sum('amount');
            
        $totalCollected = Invoice::whereIn('unit_id', $unitIds)
            ->where('created_at', '>=', $monthStart)
            ->where('status', 'paid')
            ->sum('amount');
            
        $collectionRate = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100, 1) : 0;
        
        $this->stats = [
            'properties' => count($propertyIds),
            'units' => $totalUnits,
            'occupancy_rate' => $occupancyRate,
            'collection_rate' => $collectionRate,
            'pending_maintenance' => MaintenanceRequest::whereIn('unit_id', $unitIds)
                ->where('status', 'pending')
                ->count(),
            'overdue_invoices' => Invoice::whereIn('unit_id', $unitIds)
                ->where('status', 'pending')
                ->where('due_date', '<', Carbon::now())
                ->count(),
        ];
        
        // Recent data
        $this->recentInvoices = Invoice::whereIn('unit_id', $unitIds)
            ->with(['user', 'unit.property'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
            
        $this->recentMaintenance = MaintenanceRequest::whereIn('unit_id', $unitIds)
            ->with(['user', 'unit.property'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
            
        $this->pendingNotices = VacateNotice::whereIn('unit_id', $unitIds)
            ->where('status', 'pending')
            ->with(['user', 'unit.property'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
            
        // Check subscription status
        $activeSubscription = $this->user->subscriptions()
            ->where('status', 'active')
            ->with('subscriptionPlan')
            ->first();
            
        if ($activeSubscription) {
            $this->expiringSubscription = Carbon::parse($activeSubscription->end_date)->diffInDays(Carbon::now()) <= 7;
            $this->activePlan = $activeSubscription->subscriptionPlan;
            
            // Recommend upgrade if approaching limits
            if ($totalUnits >= ($activeSubscription->subscriptionPlan->unit_limit * 0.8) || 
                count($propertyIds) >= ($activeSubscription->subscriptionPlan->property_limit * 0.8)) {
                
                $nextPlan = SubscriptionPlan::where('unit_limit', '>', $activeSubscription->subscriptionPlan->unit_limit)
                    ->orderBy('price')
                    ->first();
                    
                if ($nextPlan) {
                    $this->recommendation = [
                        'type' => 'upgrade',
                        'plan' => $nextPlan,
                        'reason' => 'You are approaching your plan limits.'
                    ];
                }
            }
        }
    }
    
    private function loadTenantDashboard()
    {
        $this->stats = [
            'total_due' => Invoice::where('user_id', $this->user->id)
                ->where('status', 'pending')
                ->sum('amount'),
            'overdue' => Invoice::where('user_id', $this->user->id)
                ->where('status', 'pending')
                ->where('due_date', '<', Carbon::now())
                ->count(),
            'maintenance_requests' => MaintenanceRequest::where('user_id', $this->user->id)->count(),
            'pending_maintenance' => MaintenanceRequest::where('user_id', $this->user->id)
                ->where('status', 'pending')
                ->count(),
        ];
        
        $this->recentInvoices = Invoice::where('user_id', $this->user->id)
            ->with(['unit.property'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
            
        $this->recentMaintenance = MaintenanceRequest::where('user_id', $this->user->id)
            ->with(['unit.property'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.main');
    }
}
