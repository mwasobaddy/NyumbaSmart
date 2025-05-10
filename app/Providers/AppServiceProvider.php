<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\PropertyRenovations\RenovationManager;
use App\Livewire\PropertyRenovations\CreateRenovation;
use App\Livewire\PropertyRenovations\RenovationDetails;
use App\Livewire\PropertyRenovations\ExpenseManagement;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Livewire Components
        Livewire::component('property-renovations.renovation-manager', RenovationManager::class);
        Livewire::component('property-renovations.create-renovation', CreateRenovation::class);
        Livewire::component('property-renovations.renovation-details', RenovationDetails::class);
        Livewire::component('property-renovations.expense-management', ExpenseManagement::class);
    }
}
