<div class="px-4 py-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-6">Tenant Onboarding</h2>

    @if($role === 'landlord')
        <div class="mb-8">
            <label for="unit-select" class="block text-sm font-medium text-gray-700 mb-1">Select Unit</label>
            <select id="unit-select" wire:model.live="selectedUnitId" wire:change="selectUnit($event.target.value)" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">-- Select Unit --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->property->name }} - Unit {{ $unit->unit_number }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Progress Tracker -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium">Onboarding Progress</h3>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Step {{ $currentStep }} of {{ $totalSteps }}</span>
        </div>
        
        <div class="relative">
            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                <div style="width: {{ ($currentStep / $totalSteps) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
            </div>
            
            <div class="grid grid-cols-3 gap-2">
                @foreach($steps as $index => $step)
                    <div class="p-4 rounded-lg border {{ $currentStep === $index ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium">{{ $step['name'] }}</h4>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold 
                                {{ $step['status'] === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $step['status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $step['status'] === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $step['status'] === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $step['status'])) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $step['description'] }}</p>
                        
                        @if($currentStep === $index)
                            <div class="mt-3">
                                @if($index === 1)
                                    @livewire('onboarding.tenant-screening', ['unitId' => $selectedUnitId], key('screening-'.$selectedUnitId))
                                @elseif($index === 2)
                                    @livewire('onboarding.lease-agreement', ['unitId' => $selectedUnitId], key('lease-'.$selectedUnitId))
                                @elseif($index === 3)
                                    @livewire('onboarding.property-inspection', ['unitId' => $selectedUnitId, 'type' => 'move_in'], key('inspection-'.$selectedUnitId))
                                @endif
                            </div>
                        @elseif($index < $currentStep)
                            <button class="mt-3 inline-flex items-center px-3 py-1.5 border border-blue-600 text-xs font-medium rounded text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                View Details
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    @if(!$selectedUnitId)
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        {{ $role === 'landlord' ? 'Please select a unit to begin the onboarding process' : 'No active onboarding process found' }}
                    </h3>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Completed Onboarding Message -->
    @if($selectedUnitId && $currentStep > $totalSteps)
        <div class="bg-green-50 border border-green-200 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Onboarding completed successfully!</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>All onboarding steps have been completed for this unit.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
