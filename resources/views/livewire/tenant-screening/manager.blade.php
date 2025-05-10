<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Tenant Screening</h2>
            <p class="text-sm text-gray-600">Manage tenant screening applications and background checks</p>
        </div>

        <!-- Session status message -->
        @if (session('status'))
            <div class="mb-4 bg-green-100 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error message -->
        @if (session('error'))
            <div class="mb-4 bg-red-100 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Landlord Interface -->
        @if ($this->isLandlord)
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="font-semibold text-lg mb-3">Create New Tenant Screening</h3>
                <form wire:submit.prevent="createScreening">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Property Selection -->
                        <div>
                            <label for="property" class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                            <select id="property" wire:model.live="selectedProperty" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Property --</option>
                                @foreach($this->properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedProperty') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Unit Selection -->
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Vacant Unit</label>
                            <select id="unit" wire:model="unitId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if(!$selectedProperty) disabled @endif>
                                <option value="">-- Select Unit --</option>
                                @foreach($this->units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_number }} ({{ $unit->property->name }})</option>
                                @endforeach
                            </select>
                            @error('unitId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tenant Selection -->
                        <div>
                            <label for="tenant" class="block text-sm font-medium text-gray-700 mb-1">Tenant</label>
                            <select id="tenant" wire:model="tenantId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select Tenant --</option>
                                @foreach($this->tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                            @error('tenantId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea id="notes" wire:model="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Add any notes about this screening request..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Create Screening Request
                        </button>
                    </div>
                </form>
            </div>

            <!-- Landlord - Existing Screenings -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="font-semibold text-lg mb-3">Screening Requests</h3>
                
                @if($this->screenings->isEmpty())
                    <p class="text-gray-500 py-4 text-center">No screening requests found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property/Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->screenings as $screening)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $screening->tenant->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $screening->tenant->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $screening->unit->property->name }}</div>
                                            <div class="text-sm text-gray-500">Unit: {{ $screening->unit->unit_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($screening->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($screening->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($screening->status === 'completed') bg-green-100 text-green-800
                                                @elseif($screening->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $screening->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $screening->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($screening->status === 'pending')
                                                <button wire:click="runBackgroundCheck({{ $screening->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Run Check</button>
                                            @endif
                                            <button wire:click="viewScreeningDetails({{ $screening->id }})" class="text-indigo-600 hover:text-indigo-900">View Details</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        <!-- Tenant Interface -->
        @else
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Your Tenant Profile</h3>
                    <button wire:click="toggleApplicationForm" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        @if($showApplicationForm) Hide Profile Form @else Update Profile @endif
                    </button>
                </div>
                
                @if($showApplicationForm)
                    <form wire:submit.prevent="submitApplication" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Personal Information -->
                            <div>
                                <label for="fullName" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" id="fullName" wire:model="fullName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('fullName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" wire:model="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input type="text" id="phone" wire:model="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="currentAddress" class="block text-sm font-medium text-gray-700">Current Address</label>
                                <input type="text" id="currentAddress" wire:model="currentAddress" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('currentAddress') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Employment Information -->
                            <div>
                                <label for="employmentStatus" class="block text-sm font-medium text-gray-700">Employment Status</label>
                                <select id="employmentStatus" wire:model="employmentStatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Select Status --</option>
                                    <option value="employed">Employed</option>
                                    <option value="self_employed">Self-Employed</option>
                                    <option value="student">Student</option>
                                    <option value="retired">Retired</option>
                                    <option value="unemployed">Unemployed</option>
                                </select>
                                @error('employmentStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="employerName" class="block text-sm font-medium text-gray-700">Employer Name</label>
                                <input type="text" id="employerName" wire:model="employerName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" @if($employmentStatus !== 'employed') disabled @endif>
                                @error('employerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="monthlyIncome" class="block text-sm font-medium text-gray-700">Monthly Income (KSH)</label>
                                <input type="number" id="monthlyIncome" wire:model="monthlyIncome" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('monthlyIncome') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Document Uploads -->
                        <div class="space-y-4">
                            <div>
                                <label for="idDocument" class="block text-sm font-medium text-gray-700">ID Document (National ID, Passport, etc.)</label>
                                <input type="file" id="idDocument" wire:model="idDocument" class="mt-1 block w-full">
                                <div wire:loading wire:target="idDocument" class="text-sm text-gray-500 mt-1">Uploading...</div>
                                @error('idDocument') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="incomeDocument" class="block text-sm font-medium text-gray-700">Income Verification (Pay Stub, Bank Statement, etc.)</label>
                                <input type="file" id="incomeDocument" wire:model="incomeDocument" class="mt-1 block w-full">
                                <div wire:loading wire:target="incomeDocument" class="text-sm text-gray-500 mt-1">Uploading...</div>
                                @error('incomeDocument') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Consent -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="consent" wire:model="consent" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="consent" class="font-medium text-gray-700">Background Check Consent</label>
                                <p class="text-gray-500">I agree to a background check, credit check, and rental history verification.</p>
                                @error('consent') <span class="text-red-500 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Save Profile Information
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-gray-600 mb-4">Please ensure your profile information is complete and up to date for faster screening approval.</p>
                @endif
            </div>

            <!-- Tenant - Existing Screenings -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="font-semibold text-lg mb-3">Your Screening Applications</h3>
                
                @if($this->screenings->isEmpty())
                    <p class="text-gray-500 py-4 text-center">No screening applications found. When landlords request a screening, it will appear here.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property/Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Landlord</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($this->screenings as $screening)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ $screening->unit->property->name }}</div>
                                            <div class="text-sm text-gray-500">Unit: {{ $screening->unit->unit_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $screening->landlord->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($screening->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($screening->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($screening->status === 'completed') bg-green-100 text-green-800
                                                @elseif($screening->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $screening->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $screening->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="viewScreeningDetails({{ $screening->id }})" class="text-indigo-600 hover:text-indigo-900">View Details</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <!-- Screening Details Modal -->
        @if($showDetails && $selectedScreening)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Screening Details</h3>
                            <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Property & Status Information -->
                            <div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Property</h4>
                                    <p class="font-medium">{{ $selectedScreening->unit->property->name }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Unit</h4>
                                    <p class="font-medium">{{ $selectedScreening->unit->unit_number }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($selectedScreening->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($selectedScreening->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($selectedScreening->status === 'completed') bg-green-100 text-green-800
                                        @elseif($selectedScreening->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $selectedScreening->status_label }}
                                    </span>
                                </div>
                                
                                @if($selectedScreening->completed_at)
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-500">Completed At</h4>
                                        <p>{{ $selectedScreening->completed_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Tenant Information -->
                            <div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Tenant</h4>
                                    <p class="font-medium">{{ $selectedScreening->tenant->name }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Contact</h4>
                                    <p>{{ $selectedScreening->tenant->email }}</p>
                                    @if($selectedScreening->tenant->phone)
                                        <p>{{ $selectedScreening->tenant->phone }}</p>
                                    @endif
                                </div>
                                
                                @if($selectedScreening->tenant->profile)
                                    <div class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-500">Employment</h4>
                                        <p>Status: {{ ucfirst($selectedScreening->tenant->profile->employment_status ?? 'Not specified') }}</p>
                                        @if($selectedScreening->tenant->profile->employer_name)
                                            <p>Employer: {{ $selectedScreening->tenant->profile->employer_name }}</p>
                                        @endif
                                        @if($selectedScreening->tenant->profile->monthly_income)
                                            <p>Monthly Income: KSH {{ number_format($selectedScreening->tenant->profile->monthly_income) }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($selectedScreening->notes)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Notes</h4>
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <p>{{ $selectedScreening->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Screening Results (if completed) -->
                        @if($selectedScreening->status === 'completed' && $reportData)
                            <div class="mt-6 border-t pt-4">
                                <h4 class="font-medium text-lg mb-3">Screening Report</h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <h5 class="font-medium mb-1">Credit Score</h5>
                                        <p class="text-2xl font-bold {{ $selectedScreening->credit_check_passed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $reportData['credit_score'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <h5 class="font-medium mb-1">Background Check</h5>
                                        <p class="text-sm flex items-center">
                                            @if($selectedScreening->background_check_passed)
                                                <svg class="h-5 w-5 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-green-600">Passed</span>
                                            @else
                                                <svg class="h-5 w-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-red-600">Failed</span>
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-md">
                                        <h5 class="font-medium mb-1">Eviction History</h5>
                                        <p class="text-sm flex items-center">
                                            @if($selectedScreening->eviction_check_passed)
                                                <svg class="h-5 w-5 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-green-600">No Evictions</span>
                                            @else
                                                <svg class="h-5 w-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-red-600">Eviction(s) Found</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Additional Report Details -->
                                @if(!empty($reportData['criminal_records']))
                                    <div class="mb-4">
                                        <h5 class="font-medium mb-1">Criminal Records</h5>
                                        <div class="bg-gray-50 p-3 rounded-md">
                                            @foreach($reportData['criminal_records'] as $record)
                                                <div class="mb-2 last:mb-0">
                                                    <p class="text-sm"><strong>Date:</strong> {{ $record['date'] }}</p>
                                                    <p class="text-sm"><strong>Offense:</strong> {{ $record['offense'] }}</p>
                                                    <p class="text-sm"><strong>Jurisdiction:</strong> {{ $record['jurisdiction'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($reportData['eviction_history']))
                                    <div class="mb-4">
                                        <h5 class="font-medium mb-1">Eviction History</h5>
                                        <div class="bg-gray-50 p-3 rounded-md">
                                            @foreach($reportData['eviction_history'] as $eviction)
                                                <div class="mb-2 last:mb-0">
                                                    <p class="text-sm"><strong>Date:</strong> {{ $eviction['date'] }}</p>
                                                    <p class="text-sm"><strong>Address:</strong> {{ $eviction['address'] }}</p>
                                                    <p class="text-sm"><strong>Reason:</strong> {{ $eviction['reason'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mt-4 text-sm text-gray-500">
                                    <p>Report ID: {{ $reportData['report_id'] }}</p>
                                    <p>Generated on {{ $reportData['background_check_date'] }}</p>
                                    <p>By {{ $reportData['report_generated_by'] }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Landlord Controls -->
                        @if($this->isLandlord)
                            <div class="mt-6 border-t pt-4">
                                @if($selectedScreening->status === 'pending')
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-medium">Run Background Check</h4>
                                            <p class="text-sm text-gray-500">Start the screening process for this tenant</p>
                                        </div>
                                        <button wire:click="runBackgroundCheck({{ $selectedScreening->id }})" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            Run Check
                                        </button>
                                    </div>
                                @endif

                                @if($selectedScreening->status === 'completed')
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="font-medium">Update Status</h4>
                                            <p class="text-sm text-gray-500">Approve or reject this tenant based on screening results</p>
                                        </div>
                                        <div class="space-x-2">
                                            <button wire:click="updateScreeningStatus({{ $selectedScreening->id }}, 'approved')" class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                                Approve
                                            </button>
                                            <button wire:click="updateScreeningStatus({{ $selectedScreening->id }}, 'rejected')" class="bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                                Reject
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <!-- Document Upload for Landlord -->
                                <div>
                                    <h4 class="font-medium mb-2">Upload Document</h4>
                                    <div class="flex items-end space-x-2">
                                        <div class="flex-1">
                                            <input type="file" wire:model="documentUpload" class="w-full">
                                            <div wire:loading wire:target="documentUpload" class="text-sm text-gray-500 mt-1">Uploading...</div>
                                            @error('documentUpload') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <button wire:click="uploadDocument" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" @if(!$documentUpload) disabled @endif>
                                            Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>