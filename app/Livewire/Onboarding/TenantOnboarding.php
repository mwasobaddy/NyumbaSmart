<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Unit;
use App\Models\User;
use App\Models\LeaseAgreement;
use App\Models\PropertyInspection;
use App\Models\TenantScreening;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TenantOnboarding extends Component
{
    use WithFileUploads;

    // Step tracking
    public $currentStep = 1;
    public $totalSteps = 4;
    public $stepTitles = [
        1 => 'Tenant Info',
        2 => 'Screening',
        3 => 'Lease',
        4 => 'Inspection'
    ];

    // Step 1: Tenant Information
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $unitId;
    public $availableUnits = [];

    // Step 2: Background Screening
    public $employmentProof;
    public $identificationProof;
    public $screeningConsent = false;

    // Step 3: Lease Agreement
    public $startDate;
    public $endDate;
    public $rentAmount;
    public $securityDeposit;
    public $termsAndConditions;
    public $specialProvisions;
    public $leaseDocument;
    public $consentToLease = false;

    // Step 4: Move-In Inspection
    public $inspectionDate;
    public $checklistItems = [
        'living_room' => [
            'walls' => ['condition' => null, 'notes' => ''],
            'flooring' => ['condition' => null, 'notes' => ''],
            'ceiling' => ['condition' => null, 'notes' => ''],
            'windows' => ['condition' => null, 'notes' => ''],
            'lighting' => ['condition' => null, 'notes' => ''],
        ],
        'kitchen' => [
            'countertops' => ['condition' => null, 'notes' => ''],
            'cabinets' => ['condition' => null, 'notes' => ''],
            'sink' => ['condition' => null, 'notes' => ''],
            'appliances' => ['condition' => null, 'notes' => ''],
            'flooring' => ['condition' => null, 'notes' => ''],
        ],
        'bathroom' => [
            'toilet' => ['condition' => null, 'notes' => ''],
            'sink' => ['condition' => null, 'notes' => ''],
            'tub_shower' => ['condition' => null, 'notes' => ''],
            'tiles' => ['condition' => null, 'notes' => ''],
            'fixtures' => ['condition' => null, 'notes' => ''],
        ],
        'bedroom' => [
            'walls' => ['condition' => null, 'notes' => ''],
            'flooring' => ['condition' => null, 'notes' => ''],
            'closet' => ['condition' => null, 'notes' => ''],
            'windows' => ['condition' => null, 'notes' => ''],
            'lighting' => ['condition' => null, 'notes' => ''],
        ],
    ];
    public $inspectionNotes;
    public $inspectionImages = [];
    public $inspectionConsent = false;

    /**
     * Set initial data when component mounts
     */
    public function mount()
    {
        // Load available units (not currently leased)
        $this->availableUnits = Unit::where('status', 'available')
            ->with('property')
            ->get();
            
        // Set default dates
        $this->inspectionDate = now()->format('Y-m-d');
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->addYear()->format('Y-m-d');
        
        // Default terms and conditions text
        $this->termsAndConditions = "This lease agreement is made between the landlord and tenant according to local housing laws. Tenant agrees to pay the monthly rent on time and maintain the property in good condition.";
    }

    /**
     * Handle moving to the next step with validation
     */
    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validateOnly('step1', [
                'firstName' => 'required|min:2',
                'lastName' => 'required|min:2',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                'unitId' => 'required|exists:units,id',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validateOnly('step2', [
                'employmentProof' => 'required|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
                'identificationProof' => 'required|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
                'screeningConsent' => 'accepted',
            ]);
        } elseif ($this->currentStep == 3) {
            $this->validateOnly('step3', [
                'startDate' => 'required|date|after_or_equal:today',
                'endDate' => 'required|date|after:startDate',
                'rentAmount' => 'required|numeric|min:1',
                'securityDeposit' => 'required|numeric|min:0',
                'termsAndConditions' => 'required',
                'leaseDocument' => 'sometimes|nullable|file|max:10240|mimes:pdf',
                'consentToLease' => 'accepted',
            ]);
        }

        $this->currentStep++;
    }

    /**
     * Handle moving back to previous step
     */
    public function previousStep()
    {
        $this->currentStep--;
    }

    /**
     * Complete the onboarding process
     */
    public function completeOnboarding()
    {
        // Validate final step
        $this->validate([
            'inspectionDate' => 'required|date',
            'inspectionConsent' => 'accepted',
            'inspectionImages.*' => 'image|max:5120',
        ]);

        // Validate that at least some checklist items are filled out
        $hasChecklist = false;
        foreach ($this->checklistItems as $area => $items) {
            foreach ($items as $item => $details) {
                if (!empty($details['condition'])) {
                    $hasChecklist = true;
                    break 2;
                }
            }
        }

        if (!$hasChecklist) {
            session()->flash('error', 'Please complete at least one inspection checklist item.');
            return;
        }

        try {
            DB::beginTransaction();

            // 1. Create user account for the tenant
            $user = new User([
                'name' => $this->firstName . ' ' . $this->lastName,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make(str_random(12)), // Generate random password, tenant will reset
                'email_verified_at' => now(),
            ]);
            $user->save();
            
            // Assign tenant role
            $user->assignRole('tenant');

            // 2. Create tenant screening record
            $screening = new TenantScreening([
                'user_id' => $user->id,
                'status' => 'approved', // Default to approved in this flow
                'notes' => 'Screening approved through onboarding process',
            ]);

            // Store uploaded documents
            if ($this->employmentProof) {
                $employmentPath = $this->employmentProof->store('tenant-screenings', 'public');
                $screening->employment_verification = $employmentPath;
            }

            if ($this->identificationProof) {
                $identificationPath = $this->identificationProof->store('tenant-screenings', 'public');
                $screening->identification = $identificationPath;
            }

            $screening->save();

            // 3. Create lease agreement
            $lease = new LeaseAgreement([
                'unit_id' => $this->unitId,
                'tenant_id' => $user->id,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'rent_amount' => $this->rentAmount,
                'security_deposit' => $this->securityDeposit,
                'terms' => $this->termsAndConditions,
                'special_provisions' => $this->specialProvisions,
                'status' => 'active',
            ]);

            // Store lease document if uploaded
            if ($this->leaseDocument) {
                $leasePath = $this->leaseDocument->store('lease-agreements', 'public');
                $lease->document_path = $leasePath;
            }

            $lease->save();

            // 4. Create property inspection
            $inspection = new PropertyInspection([
                'unit_id' => $this->unitId,
                'tenant_id' => $user->id, 
                'inspection_date' => $this->inspectionDate,
                'inspection_type' => 'move_in',
                'status' => 'completed',
                'notes' => $this->inspectionNotes,
                'checklist_items' => json_encode($this->checklistItems),
            ]);

            $inspection->save();

            // Store inspection images
            if ($this->inspectionImages && count($this->inspectionImages) > 0) {
                $imagePaths = [];
                foreach ($this->inspectionImages as $image) {
                    $imagePaths[] = $image->store('inspection-images', 'public');
                }
                $inspection->image_paths = json_encode($imagePaths);
                $inspection->save();
            }

            // 5. Update unit status to occupied
            $unit = Unit::find($this->unitId);
            $unit->status = 'occupied';
            $unit->current_tenant_id = $user->id;
            $unit->save();

            DB::commit();

            // Send welcome email or notification to tenant
            // This would be implemented in a real app
            // Mail::to($user->email)->send(new TenantWelcome($user));

            session()->flash('message', 'Tenant onboarding completed successfully!');
            $this->reset();
            $this->mount(); // Reload the component with fresh data
            $this->currentStep = 1;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred during the onboarding process: ' . $e->getMessage());
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.onboarding.tenant-onboarding');
    }
}
