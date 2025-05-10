<?php

namespace App\Http\Controllers;

use App\Models\TenantScreening;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TenantScreeningController extends Controller
{
    /**
     * Display a listing of tenant screenings.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Landlord')) {
            $screenings = TenantScreening::where('landlord_id', $user->id)
                ->with(['tenant', 'unit.property'])
                ->latest()
                ->paginate(10);
            
            return view('tenant-screening.index', [
                'screenings' => $screenings,
                'isLandlord' => true
            ]);
        } else {
            // For tenants, show their screening applications
            $screenings = TenantScreening::where('tenant_id', $user->id)
                ->with(['landlord', 'unit.property'])
                ->latest()
                ->paginate(10);
            
            return view('tenant-screening.index', [
                'screenings' => $screenings,
                'isLandlord' => false
            ]);
        }
    }

    /**
     * Show the form for creating a new tenant screening application.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Landlord')) {
            // Get available properties and units for landlord
            $units = Unit::whereHas('property', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'vacant')->with('property')->get();
            
            // Get users who are tenants and don't have an active screening
            $tenants = User::role('Tenant')
                ->whereDoesntHave('tenantScreenings', function($query) {
                    $query->where('status', '!=', 'rejected')
                        ->where('status', '!=', 'completed');
                })
                ->get();
            
            return view('tenant-screening.create', [
                'tenants' => $tenants,
                'units' => $units
            ]);
        } else {
            // Redirect tenants who can't access this page
            return redirect()->route('tenant-screening.index')
                ->with('error', 'You do not have permission to create screening applications.');
        }
    }

    /**
     * Store a newly created tenant screening in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('Landlord')) {
            return redirect()->route('tenant-screening.index')
                ->with('error', 'Only landlords can create screening applications.');
        }
        
        $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'unit_id' => 'required|exists:units,id',
            'notes' => 'nullable|string'
        ]);
        
        // Verify that the unit belongs to the landlord
        $unit = Unit::findOrFail($request->unit_id);
        if ($unit->property->user_id != $user->id) {
            return redirect()->route('tenant-screening.index')
                ->with('error', 'You do not have permission to screen tenants for this unit.');
        }
        
        $screening = TenantScreening::create([
            'tenant_id' => $request->tenant_id,
            'landlord_id' => $user->id,
            'unit_id' => $request->unit_id,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('tenant-screening.show', $screening)
            ->with('status', 'Tenant screening application has been created successfully.');
    }

    /**
     * Display the specified tenant screening.
     */
    public function show(TenantScreening $tenantScreening)
    {
        $user = Auth::user();
        
        // Check if user is authorized to view this screening
        if ($user->id != $tenantScreening->landlord_id && $user->id != $tenantScreening->tenant_id && !$user->hasRole('Admin')) {
            return redirect()->route('tenant-screening.index')
                ->with('error', 'You do not have permission to view this screening application.');
        }
        
        // Load relationships
        $tenantScreening->load(['tenant', 'landlord', 'unit.property']);
        
        return view('tenant-screening.show', [
            'screening' => $tenantScreening,
            'isLandlord' => $user->hasRole('Landlord')
        ]);
    }

    /**
     * Update tenant screening status and checks.
     */
    public function update(Request $request, TenantScreening $tenantScreening)
    {
        $user = Auth::user();
        
        // Only landlord who created the screening can update it
        if ($user->id != $tenantScreening->landlord_id && !$user->hasRole('Admin')) {
            return redirect()->route('tenant-screening.index')
                ->with('error', 'You do not have permission to update this screening application.');
        }
        
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'credit_check_passed' => 'nullable|boolean',
            'background_check_passed' => 'nullable|boolean',
            'eviction_check_passed' => 'nullable|boolean',
            'employment_verified' => 'nullable|boolean',
            'income_verified' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
        
        // Only update fields that were included in the request
        $data = $request->only([
            'status', 
            'credit_check_passed',
            'background_check_passed',
            'eviction_check_passed',
            'employment_verified',
            'income_verified',
            'notes'
        ]);
        
        // Handle document upload if present
        if ($request->hasFile('document')) {
            if ($tenantScreening->document_path) {
                // Delete old document if it exists
                Storage::delete($tenantScreening->document_path);
            }
            
            $path = $request->file('document')->store('tenant_screenings');
            $data['document_path'] = $path;
        }
        
        // If status is being updated to 'completed', set completed_at timestamp
        if ($request->status == 'completed' && $tenantScreening->status != 'completed') {
            $data['completed_at'] = now();
        }
        
        $tenantScreening->update($data);
        
        return redirect()->route('tenant-screening.show', $tenantScreening)
            ->with('status', 'Tenant screening has been updated successfully.');
    }

    /**
     * Submit tenant application form
     */
    public function submitApplication(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('Tenant')) {
            return redirect()->route('dashboard')
                ->with('error', 'Only tenants can submit screening applications.');
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'current_address' => 'required|string|max:255',
            'employment_status' => 'required|string|in:employed,self_employed,unemployed,retired,student',
            'employer_name' => 'required_if:employment_status,employed|nullable|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'identification_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'proof_of_income' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'consent' => 'required|accepted',
        ]);
        
        try {
            // Store application documents
            $idPath = $request->file('identification_document')->store('tenant_applications/id');
            $incomePath = $request->file('proof_of_income')->store('tenant_applications/income');
            
            // Update user profile with additional information
            $user->update([
                'phone' => $request->phone,
                'address' => $request->current_address,
            ]);
            
            // Store additional information in user metadata or profile
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employment_status' => $request->employment_status,
                    'employer_name' => $request->employer_name,
                    'monthly_income' => $request->monthly_income,
                    'id_document_path' => $idPath,
                    'income_document_path' => $incomePath,
                    'application_submitted_at' => now(),
                ]
            );
            
            return redirect()->route('tenant-screening.index')
                ->with('status', 'Your application has been submitted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to submit tenant application: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to submit your application. Please try again later.');
        }
    }

    /**
     * Run background check on tenant.
     */
    public function runBackgroundCheck(TenantScreening $tenantScreening)
    {
        $user = Auth::user();
        
        // Only landlord who created the screening can run checks
        if ($user->id != $tenantScreening->landlord_id && !$user->hasRole('Admin')) {
            return redirect()->route('tenant-screening.index')
                ->with('error', 'You do not have permission to run checks on this tenant.');
        }
        
        // In a real implementation, this would connect to a third-party API
        // For now, we'll simulate the process with random results
        
        try {
            // Update screening status to in progress
            $tenantScreening->update(['status' => 'in_progress']);
            
            // Simulate API delay
            sleep(1);
            
            // For simulation purposes, generate random check results
            $checkResults = [
                'credit_check_passed' => random_int(0, 1) === 1,
                'background_check_passed' => random_int(0, 1) === 1,
                'eviction_check_passed' => random_int(0, 1) === 1,
                'employment_verified' => random_int(0, 1) === 1,
                'income_verified' => random_int(0, 1) === 1,
            ];
            
            // Create simulated report data
            $reportData = [
                'credit_score' => random_int(300, 850),
                'credit_report_date' => now()->format('Y-m-d'),
                'background_check_date' => now()->format('Y-m-d'),
                'eviction_history' => $checkResults['eviction_check_passed'] ? [] : ['2023-01-15 - Property damage reported'],
                'criminal_records' => $checkResults['background_check_passed'] ? [] : ['2022-05-03 - Minor offense'],
                'verification_details' => [
                    'employment_verified_on' => $checkResults['employment_verified'] ? now()->format('Y-m-d') : null,
                    'income_verified_on' => $checkResults['income_verified'] ? now()->format('Y-m-d') : null,
                ],
                'report_generated_by' => 'NyumbaSmart System (Simulation)',
                'report_id' => 'SIM-' . strtoupper(substr(md5(time()), 0, 10)),
            ];
            
            // Update the screening record with results
            $tenantScreening->update([
                'credit_check_passed' => $checkResults['credit_check_passed'],
                'background_check_passed' => $checkResults['background_check_passed'],
                'eviction_check_passed' => $checkResults['eviction_check_passed'],
                'employment_verified' => $checkResults['employment_verified'],
                'income_verified' => $checkResults['income_verified'],
                'report_data' => $reportData,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
            
            return redirect()->route('tenant-screening.show', $tenantScreening)
                ->with('status', 'Background check has been completed successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to run background check: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Failed to run background check. Please try again later.');
        }
    }

    /**
     * Download tenant screening document.
     */
    public function downloadDocument(TenantScreening $tenantScreening)
    {
        $user = Auth::user();
        
        // Check if user is authorized to download this document
        if ($user->id != $tenantScreening->landlord_id && $user->id != $tenantScreening->tenant_id && !$user->hasRole('Admin')) {
            return redirect()->route('tenant-screening.index')
                ->with('error', 'You do not have permission to download this document.');
        }
        
        // Check if document exists
        if (!$tenantScreening->document_path || !Storage::exists($tenantScreening->document_path)) {
            return back()->with('error', 'Document not found.');
        }
        
        return Storage::download($tenantScreening->document_path, 'tenant_screening_report.pdf');
    }
}