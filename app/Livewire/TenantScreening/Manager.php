<?php

namespace App\Livewire\TenantScreening;

use App\Models\Property;
use App\Models\TenantScreening;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\BackgroundCheckService;
use App\Notifications\ScreeningRequestNotification;
use App\Notifications\ScreeningResultNotification;
use App\Notifications\ScreeningStatusUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Manager extends Component
{
    use WithFileUploads, WithPagination;

    // Livewire properties for landlord functionality
    public $selectedProperty = null;
    public $unitId = null;
    public $tenantId = null;
    public $notes = '';

    // Properties for tenant profile form
    public $showApplicationForm = false;
    public $fullName = '';
    public $email = '';
    public $phone = '';
    public $currentAddress = '';
    public $employmentStatus = '';
    public $employerName = '';
    public $monthlyIncome = 0;
    public $idDocument = null;
    public $incomeDocument = null;
    public $consent = false;

    // Properties for managing screening details
    public $showDetails = false;
    public $selectedScreening = null;
    public $documentUpload = null;
    public $reportData = null;
    
    // Properties for approval/rejection workflow
    public $rejectionReason = '';
    public $additionalRequirements = '';
    public $securityDepositMultiplier = 1;
    public $requireCosigner = false;

    // Rules for form validation
    protected $rules = [
        'unitId' => 'required_if:isLandlord,true',
        'tenantId' => 'required_if:isLandlord,true',
        'fullName' => 'required_if:isLandlord,false|max:255',
        'email' => 'required_if:isLandlord,false|email|max:255',
        'phone' => 'required_if:isLandlord,false|string|max:20',
        'currentAddress' => 'required_if:isLandlord,false|string|max:255',
        'employmentStatus' => 'required_if:isLandlord,false',
        'employerName' => 'required_if:employmentStatus,employed',
        'monthlyIncome' => 'required_if:isLandlord,false|numeric|min:0',
        'idDocument' => 'required_if:isLandlord,false|file|max:10240|mimes:pdf,jpg,jpeg,png',
        'incomeDocument' => 'required_if:isLandlord,false|file|max:10240|mimes:pdf,jpg,jpeg,png',
        'consent' => 'required_if:isLandlord,false|accepted',
    ];

    /**
     * Mount the component and initialize properties
     */
    public function mount()
    {
        // Set user data if they have a profile
        $user = Auth::user();
        if ($user && !$this->isLandlord && $user->profile) {
            $this->fullName = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
            $this->currentAddress = $user->address ?? '';
            
            if ($user->profile) {
                $this->employmentStatus = $user->profile->employment_status ?? '';
                $this->employerName = $user->profile->employer_name ?? '';
                $this->monthlyIncome = $user->profile->monthly_income ?? 0;
            }
        }
    }

    /**
     * Get the user role as a computed property
     */
    public function getIsLandlordProperty()
    {
        return Auth::user() && Auth::user()->role === 'landlord';
    }

    /**
     * Get properties for the property dropdown (landlord only)
     */
    public function getPropertiesProperty()
    {
        if (!$this->isLandlord) {
            return collect();
        }
        
        return Property::where('user_id', Auth::id())->get();
    }

    /**
     * Get units for the selected property (landlord only)
     */
    public function getUnitsProperty()
    {
        if (!$this->selectedProperty) {
            return collect();
        }
        
        return Unit::where('property_id', $this->selectedProperty)
            ->where('status', 'vacant')
            ->with('property')
            ->get();
    }

    /**
     * Get tenants for tenant dropdown (landlord only)
     */
    public function getTenantsProperty()
    {
        if (!$this->isLandlord) {
            return collect();
        }
        
        return User::where('role', 'tenant')->get();
    }

    /**
     * Get screenings for the current user
     */
    public function getScreeningsProperty()
    {
        $user = Auth::user();
        
        if ($this->isLandlord) {
            return TenantScreening::where('landlord_id', $user->id)
                ->with(['tenant', 'landlord', 'unit.property'])
                ->orderByDesc('created_at')
                ->get();
        } else {
            return TenantScreening::where('tenant_id', $user->id)
                ->with(['tenant', 'landlord', 'unit.property'])
                ->orderByDesc('created_at')
                ->get();
        }
    }

    /**
     * Create a new tenant screening request (landlord only)
     */
    public function createScreening()
    {
        if (!$this->isLandlord) {
            return;
        }
        
        $this->validate([
            'unitId' => 'required',
            'tenantId' => 'required',
        ]);
        
        $screening = new TenantScreening();
        $screening->landlord_id = Auth::id();
        $screening->tenant_id = $this->tenantId;
        $screening->unit_id = $this->unitId;
        $screening->notes = $this->notes;
        $screening->status = 'pending';
        $screening->save();
        
        // Send notification to tenant
        $tenant = User::find($this->tenantId);
        $tenant->notify(new ScreeningRequestNotification($screening));
        
        // Reset form fields
        $this->reset(['unitId', 'tenantId', 'notes']);
        
        session()->flash('status', 'Screening request has been created successfully and tenant has been notified.');
    }

    /**
     * Toggle the tenant application form display
     */
    public function toggleApplicationForm()
    {
        $this->showApplicationForm = !$this->showApplicationForm;
    }

    /**
     * Submit tenant profile/application
     */
    public function submitApplication()
    {
        if ($this->isLandlord) {
            return;
        }
        
        $this->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'currentAddress' => 'required|string|max:255',
            'employmentStatus' => 'required|string',
            'employerName' => 'nullable|string|max:255',
            'monthlyIncome' => 'required|numeric|min:0',
            'idDocument' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'incomeDocument' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'consent' => 'required|accepted',
        ]);
        
        $user = Auth::user();
        
        // Update user basic info
        $user->name = $this->fullName;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->address = $this->currentAddress;
        $user->save();
        
        // Store ID document
        $idDocumentPath = $this->idDocument->store('tenant-documents/id', 'public');
        
        // Store income document
        $incomeDocumentPath = $this->incomeDocument->store('tenant-documents/income', 'public');
        
        // Create or update user profile
        $profile = $user->profile ?? new UserProfile();
        $profile->user_id = $user->id;
        $profile->employment_status = $this->employmentStatus;
        $profile->employer_name = $this->employerName;
        $profile->monthly_income = $this->monthlyIncome;
        $profile->id_document_path = $idDocumentPath;
        $profile->income_document_path = $incomeDocumentPath;
        $profile->application_submitted_at = now();
        $profile->save();
        
        $this->reset([
            'showApplicationForm', 'idDocument', 'incomeDocument', 'consent'
        ]);
        
        session()->flash('status', 'Your tenant profile has been updated successfully.');
    }

    /**
     * Run a background check on a tenant
     */
    public function runBackgroundCheck($id)
    {
        try {
            $screening = TenantScreening::findOrFail($id);
            
            // Check if user is authorized
            if (Auth::id() != $screening->landlord_id) {
                session()->flash('error', 'You are not authorized to perform this action.');
                return;
            }
            
            // Check if tenant has completed their profile
            $tenant = $screening->tenant;
            $profile = $tenant->profile;
            
            if (!$profile || !$profile->monthly_income || !$profile->id_document_path || !$profile->income_document_path) {
                session()->flash('error', 'Tenant has not completed their profile yet.');
                return;
            }
            
            // Update status to processing
            $screening->status = 'processing';
            $screening->save();
            
            // Run the background check using our service
            $backgroundCheckService = new BackgroundCheckService();
            $result = $backgroundCheckService->initiateCheck($screening);
            
            if ($result['success']) {
                // Save reference ID to screening record
                $reportData = [
                    'reference_id' => $result['reference_id'],
                    'check_url' => $result['check_url'] ?? null,
                    'initiated_at' => now()->format('Y-m-d H:i:s'),
                    'status' => 'processing'
                ];
                
                $screening->report_data = json_encode($reportData);
                $screening->status = 'processing';
                $screening->save();
                
                // Notify the tenant that their screening is being processed
                $tenant->notify(new ScreeningStatusUpdateNotification($screening, 'processing'));
                
                session()->flash('status', 'Background check initiated successfully. You will be notified when results are available.');
            } else {
                session()->flash('error', 'Failed to initiate background check: ' . $result['message']);
                $screening->status = 'pending';
                $screening->save();
            }
            
        } catch (\Exception $e) {
            Log::error('Background check error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while running the background check: ' . $e->getMessage());
        }
    }

    /**
     * Check background check status
     */
    public function checkBackgroundStatus($id)
    {
        try {
            $screening = TenantScreening::findOrFail($id);
            
            // Check if user is authorized
            if (Auth::id() != $screening->landlord_id) {
                session()->flash('error', 'You are not authorized to perform this action.');
                return;
            }
            
            // Check if we have a reference ID
            $reportData = json_decode($screening->report_data ?? '{}', true);
            $referenceId = $reportData['reference_id'] ?? null;
            
            if (!$referenceId) {
                session()->flash('error', 'No background check has been initiated for this screening.');
                return;
            }
            
            // Get status from service
            $backgroundCheckService = new BackgroundCheckService();
            $result = $backgroundCheckService->getCheckStatus($referenceId);
            
            if ($result['success']) {
                // Update report data
                $reportData = array_merge($reportData, $result['report_data'] ?? []);
                $reportData['status'] = $result['status'];
                $reportData['last_checked_at'] = now()->format('Y-m-d H:i:s');
                
                $screening->report_data = json_encode($reportData);
                
                // If check is complete, update screening status
                if ($result['status'] === 'completed') {
                    $screening->status = 'completed';
                    
                    // Notify the tenant that their screening is complete
                    $tenant = $screening->tenant;
                    $tenant->notify(new ScreeningResultNotification($screening));
                }
                
                $screening->save();
                
                // Show the screening details with updated results
                $this->viewScreening($screening->id);
                
                session()->flash('status', 'Background check status updated successfully.');
            } else {
                session()->flash('error', 'Failed to check background status: ' . $result['message']);
            }
            
        } catch (\Exception $e) {
            Log::error('Background check status error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while checking the background status: ' . $e->getMessage());
        }
    }

    /**
     * Approve a tenant screening
     */
    public function approveScreening()
    {
        if (!$this->selectedScreening || !$this->isLandlord) {
            return;
        }
        
        if ($this->selectedScreening->landlord_id != Auth::id()) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }
        
        $this->selectedScreening->status = 'approved';
        
        // Add any additional requirements
        $additionalInfo = [
            'security_deposit_multiplier' => $this->securityDepositMultiplier,
            'require_cosigner' => $this->requireCosigner,
            'additional_requirements' => $this->additionalRequirements,
            'approved_by' => Auth::id(),
            'approved_at' => now()->format('Y-m-d H:i:s'),
        ];
        
        // Combine with existing report data
        $reportData = json_decode($this->selectedScreening->report_data ?? '{}', true);
        $reportData['approval_info'] = $additionalInfo;
        $this->selectedScreening->report_data = json_encode($reportData);
        
        $this->selectedScreening->save();
        
        // Notify tenant about approval
        $tenant = $this->selectedScreening->tenant;
        $tenant->notify(new ScreeningStatusUpdateNotification($this->selectedScreening, 'approved'));
        
        $this->closeDetails();
        session()->flash('status', 'The tenant has been approved successfully and notified.');
    }

    /**
     * Reject a tenant screening
     */
    public function rejectScreening()
    {
        if (!$this->selectedScreening || !$this->isLandlord) {
            return;
        }
        
        if ($this->selectedScreening->landlord_id != Auth::id()) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }
        
        $this->validate([
            'rejectionReason' => 'required|string|min:10',
        ]);
        
        $this->selectedScreening->status = 'rejected';
        
        // Add rejection details to report data
        $reportData = json_decode($this->selectedScreening->report_data ?? '{}', true);
        $reportData['rejection_info'] = [
            'reason' => $this->rejectionReason,
            'rejected_by' => Auth::id(),
            'rejected_at' => now()->format('Y-m-d H:i:s'),
        ];
        $this->selectedScreening->report_data = json_encode($reportData);
        
        $this->selectedScreening->save();
        
        // Notify tenant about rejection
        $tenant = $this->selectedScreening->tenant;
        $tenant->notify(new ScreeningStatusUpdateNotification($this->selectedScreening, 'rejected'));
        
        $this->closeDetails();
        $this->rejectionReason = '';
        
        session()->flash('status', 'The tenant has been rejected and notified.');
    }

    /**
     * Upload additional documents for a screening
     */
    public function uploadDocument()
    {
        if (!$this->selectedScreening) {
            return;
        }
        
        $this->validate([
            'documentUpload' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);
        
        // Check if user is authorized
        $user = Auth::user();
        if (($user->role === 'landlord' && $user->id !== $this->selectedScreening->landlord_id) || 
            ($user->role === 'tenant' && $user->id !== $this->selectedScreening->tenant_id)) {
            session()->flash('error', 'You are not authorized to perform this action.');
            return;
        }
        
        // Store the document
        $path = $this->documentUpload->store('tenant-screening/documents', 'public');
        
        // Update the screening document path - we'll store multiple documents as a JSON array
        $documents = json_decode($this->selectedScreening->document_path ?? '[]', true);
        $documents[] = [
            'path' => $path,
            'name' => $this->documentUpload->getClientOriginalName(),
            'uploaded_by' => $user->role,
            'uploaded_at' => now()->format('Y-m-d H:i:s'),
        ];
        
        $this->selectedScreening->document_path = json_encode($documents);
        $this->selectedScreening->save();
        
        $this->documentUpload = null;
        session()->flash('status', 'Document uploaded successfully.');
    }

    /**
     * View screening details and report
     */
    public function viewScreening($id)
    {
        $this->selectedScreening = TenantScreening::with(['tenant', 'landlord', 'unit.property'])
            ->findOrFail($id);
        
        // Parse report data if available
        if (!empty($this->selectedScreening->report_data)) {
            $this->reportData = json_decode($this->selectedScreening->report_data, true);
        } else {
            $this->reportData = null;
        }
        
        $this->showDetails = true;
    }

    /**
     * Close the screening details view
     */
    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedScreening = null;
        $this->reportData = null;
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.tenant-screening.manager');
    }
}