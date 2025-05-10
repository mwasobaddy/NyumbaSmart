<div class="w-full">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-6 text-center">Tenant Onboarding</h2>
        
        <!-- Progress Steps -->
        <div class="flex justify-center mb-8">
            <div class="w-full max-w-3xl">
                <div class="flex items-center">
                    @foreach(range(1, $totalSteps) as $step)
                        <div class="flex-1 relative">
                            <div class="flex items-center justify-center">
                                <button 
                                    type="button" 
                                    class="w-10 h-10 rounded-full flex items-center justify-center 
                                    {{ $currentStep > $step ? 'bg-green-500 text-white' : 
                                       ($currentStep == $step ? 'bg-blue-500 text-white' : 
                                       'bg-gray-200 text-gray-600') }}"
                                    wire:click="$set('currentStep', {{ $step }})"
                                    {{ $currentStep < $step ? 'disabled' : '' }}
                                >
                                    @if($currentStep > $step)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        {{ $step }}
                                    @endif
                                </button>
                            </div>
                            <div class="text-xs text-center mt-2">{{ $stepTitles[$step] }}</div>
                            
                            @if($step < $totalSteps)
                                <div class="w-full absolute top-5 px-8">
                                    <div class="h-1 {{ $currentStep > $step ? 'bg-green-500' : 'bg-gray-200' }} w-full"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Step 1: Tenant Information -->
        <div class="{{ $currentStep != 1 ? 'hidden' : '' }}">
            <h3 class="text-xl font-semibold mb-4">Tenant Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700">First Name</label>
                    <input type="text" wire:model="firstName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="First Name">
                    @error('firstName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700">Last Name</label>
                    <input type="text" wire:model="lastName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Last Name">
                    @error('lastName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700">Email Address</label>
                    <input type="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Email">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700">Phone Number</label>
                    <input type="text" wire:model="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Phone">
                    @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-gray-700">Select Unit</label>
                    <select wire:model="unitId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">-- Select Unit --</option>
                        @foreach($availableUnits as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->unit_number }} at {{ $unit->property->name }} ({{ $unit->property->address }})
                            </option>
                        @endforeach
                    </select>
                    @error('unitId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Step 2: Background Screening -->
        <div class="{{ $currentStep != 2 ? 'hidden' : '' }}">
            <h3 class="text-xl font-semibold mb-4">Background Screening</h3>
            
            <div class="mb-4">
                <p class="text-gray-700 mb-4">
                    To proceed with your application, we require the following documents to verify your information.
                    Please upload clear, legible copies of the following documents:
                </p>
                
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Proof of Employment (Pay stubs, employment letter)</label>
                    <input type="file" wire:model="employmentProof" class="mt-1 block w-full">
                    <div wire:loading wire:target="employmentProof">Uploading...</div>
                    @error('employmentProof') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Identification (Driver's License, Passport, ID Card)</label>
                    <input type="file" wire:model="identificationProof" class="mt-1 block w-full">
                    <div wire:loading wire:target="identificationProof">Uploading...</div>
                    @error('identificationProof') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                <h4 class="text-lg font-medium text-blue-800 mb-2">Background Check Consent</h4>
                <p class="text-blue-700 mb-4">
                    By checking the box below, I authorize the property management to obtain and review my:
                </p>
                <ul class="list-disc pl-5 text-blue-700 mb-4">
                    <li>Credit report</li>
                    <li>Criminal background check</li>
                    <li>Eviction history</li>
                    <li>Employment and income verification</li>
                </ul>
                
                <div class="flex items-center">
                    <input type="checkbox" wire:model="screeningConsent" id="screeningConsent" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="screeningConsent" class="ml-2 block text-blue-800">
                        I consent to background screening
                    </label>
                </div>
                @error('screeningConsent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Step 3: Lease Agreement -->
        <div class="{{ $currentStep != 3 ? 'hidden' : '' }}">
            <h3 class="text-xl font-semibold mb-4">Lease Agreement</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-gray-700">Lease Start Date</label>
                    <input type="date" wire:model="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700">Lease End Date</label>
                    <input type="date" wire:model="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700">Monthly Rent Amount ($)</label>
                    <input type="number" step="0.01" wire:model="rentAmount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('rentAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-gray-700">Security Deposit Amount ($)</label>
                    <input type="number" step="0.01" wire:model="securityDeposit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    @error('securityDeposit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Terms and Conditions</label>
                <textarea wire:model="termsAndConditions" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                @error('termsAndConditions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Special Provisions</label>
                <textarea wire:model="specialProvisions" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                @error('specialProvisions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Upload Lease Document (PDF)</label>
                <input type="file" wire:model="leaseDocument" class="mt-1 block w-full" accept="application/pdf">
                <div wire:loading wire:target="leaseDocument">Uploading...</div>
                @error('leaseDocument') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-6">
                <div class="flex items-center">
                    <input type="checkbox" wire:model="consentToLease" id="leaseConsent" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                    <label for="leaseConsent" class="ml-2 block text-green-800">
                        I have read and agree to the lease terms and conditions
                    </label>
                </div>
                @error('consentToLease') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Step 4: Move-In Inspection -->
        <div class="{{ $currentStep != 4 ? 'hidden' : '' }}">
            <h3 class="text-xl font-semibold mb-4">Move-In Inspection</h3>
            
            <div class="mb-6">
                <label class="block text-gray-700">Inspection Date</label>
                <input type="date" wire:model="inspectionDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error('inspectionDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <!-- Inspection Checklist -->
            <div class="mb-6">
                <h4 class="font-semibold text-lg mb-2">Property Condition Checklist</h4>
                
                @foreach(['living_room', 'kitchen', 'bathroom', 'bedroom'] as $area)
                    <div class="mb-4">
                        <h5 class="font-medium capitalize mb-2">{{ str_replace('_', ' ', $area) }}</h5>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            @foreach($checklistItems[$area] as $item => $details)
                                <div class="mb-3 pb-3 border-b border-gray-200 last:border-0">
                                    <div class="font-medium capitalize mb-2">{{ str_replace('_', ' ', $item) }}</div>
                                    
                                    <div class="flex items-center space-x-4 mb-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" wire:model="checklistItems.{{ $area }}.{{ $item }}.condition" value="good" class="form-radio text-blue-600">
                                            <span class="ml-2">Good</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" wire:model="checklistItems.{{ $area }}.{{ $item }}.condition" value="fair" class="form-radio text-yellow-600">
                                            <span class="ml-2">Fair</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" wire:model="checklistItems.{{ $area }}.{{ $item }}.condition" value="poor" class="form-radio text-red-600">
                                            <span class="ml-2">Poor</span>
                                        </label>
                                    </div>
                                    
                                    <div>
                                        <input type="text" wire:model="checklistItems.{{ $area }}.{{ $item }}.notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Notes (optional)">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Additional Notes</label>
                <textarea wire:model="inspectionNotes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                @error('inspectionNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Upload Inspection Photos (multiple allowed)</label>
                <input type="file" wire:model="inspectionImages" class="mt-1 block w-full" multiple accept="image/*">
                <div wire:loading wire:target="inspectionImages">Uploading...</div>
                @error('inspectionImages.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 mb-6">
                <div class="flex items-center">
                    <input type="checkbox" wire:model="inspectionConsent" id="inspectionConsent" class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                    <label for="inspectionConsent" class="ml-2 block text-purple-800">
                        I confirm that this inspection report accurately reflects the condition of the property
                    </label>
                </div>
                @error('inspectionConsent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
            @if($currentStep > 1)
                <button wire:click="previousStep" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    Previous
                </button>
            @else
                <div></div>
            @endif
            
            @if($currentStep < $totalSteps)
                <button wire:click="nextStep" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Next
                </button>
            @else
                <button wire:click="completeOnboarding" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Complete Onboarding
                </button>
            @endif
        </div>
    </div>
</div>
