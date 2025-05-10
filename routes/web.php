<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TenantScreening\Manager;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Property Management Routes
    Route::get('/properties', function () {
        return view('properties.index');
    })->name('properties.index');
    
    Route::get('/properties/{property}/units', function () {
        return view('units.index');
    })->name('units.index');
    
    // Invoice & Billing Routes
    Route::get('/invoices', function () {
        return view('invoices.index');
    })->name('invoices.index');
    
    // Maintenance Request Routes
    Route::get('/maintenance', function () {
        return view('maintenance.index');
    })->name('maintenance.index');
    
    // Vacate Notice Routes
    Route::get('/vacate-notices', function () {
        return view('vacate.index');
    })->name('vacate.index');
    
    // Reviews Routes
    Route::get('/reviews', function () {
        return view('reviews.index');
    })->name('reviews.index');
    
    // Subscription Plans Routes
    Route::get('/subscription-plans', function () {
        return view('subscriptions.plans');
    })->name('subscriptions.plans');
    
    Route::get('/my-subscription', function () {
        return view('subscriptions.my');
    })->name('subscriptions.my');
    
    // Tenant Screening Routes
    Route::get('/tenant-screening', Manager::class)->name('tenant-screening.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('properties', App\Http\Controllers\PropertyController::class);
    Route::resource('properties.units', App\Http\Controllers\UnitController::class);
    Route::get('subscriptions/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscriptions.plans');
    Route::post('subscriptions/checkout', [App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
    Route::resource('maintenance', App\Http\Controllers\MaintenanceRequestController::class);
    Route::resource('reviews', App\Http\Controllers\ReviewController::class);
    Route::resource('vacate', App\Http\Controllers\VacateNoticeController::class);

    // M-Pesa Routes
    Route::get('/mpesa/simulator', [App\Http\Controllers\MpesaController::class, 'showSimulator'])->name('mpesa.simulator');
    Route::get('/mpesa/payments', function () {
        return view('mpesa.payments');
    })->name('mpesa.payments');
    
    // Tenant Screening Routes
    Route::resource('tenant-screening', App\Http\Controllers\TenantScreeningController::class);
    Route::post('tenant-screening/submit-application', [App\Http\Controllers\TenantScreeningController::class, 'submitApplication'])->name('tenant-screening.submit-application');
    Route::post('tenant-screening/{tenantScreening}/run-check', [App\Http\Controllers\TenantScreeningController::class, 'runBackgroundCheck'])->name('tenant-screening.run-check');
    Route::get('tenant-screening/{tenantScreening}/download', [App\Http\Controllers\TenantScreeningController::class, 'downloadDocument'])->name('tenant-screening.download-document');
});

require __DIR__.'/auth.php';
