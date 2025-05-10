<div>
    @if(!$unitId)
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-md">
            <p class="text-yellow-700">Please select a unit to begin tenant screening.</p>
        </div>
    @elseif(!$screening && $role === 'landlord')
        <div class="mb-4">
            <button wire:click="createScreening" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Begin Screening Process
            </button>
        </div>
    @elseif($screening)
        <div class="mb-4 bg-white rounded-lg border p-4">
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-lg font-medium">Screening Details</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ $screening->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $screening->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $screening->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $screening->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                ">
                    {{ ucfirst($screening->status) }}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Tenant</p>
                    <p class="font-medium">{{ $screening->tenant->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Landlord</p>
                    <p class="font-medium">{{ $screening->landlord->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Unit</p>
                    <p class="font-medium">{{ $screening->unit->property->name }} - Unit {{ $screening->unit->unit_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Created</p>
                    <p class="font-medium">{{ $screening->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            
            <div class="mb-4">
                <h4 class="text-sm font-medium mb-2">Verification Results</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <div class="flex items-center">
                        <span class="h-5 w-5 flex items-center justify-center rounded-full {{ $screening->credit_check_passed ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} mr-2">
                            @if($screening->credit_check_passed)
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </span>
                        <span class="text-sm">Credit Check</span>
                    </div>
                    <div class="flex items-center">
                        <span class="h-5 w-5 flex items-center justify-center rounded-full {{ $screening->background_check_passed ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} mr-2">
                            @if($screening->background_check_passed)
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </span>
                        <span class="text-sm">Background Check</span>
                    </div>
                    <div class="flex items-center">
                        <span class="h-5 w-5 flex items-center justify-center rounded-full {{ $screening->eviction_check_passed ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} mr-2">
                            @if($screening->eviction_check_passed)
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </span>
                        <span class="text-sm">Eviction History</span>
                    </div>
                    <div class="flex items-center">
                        <span class="h-5 w-5 flex items-center justify-center rounded-full {{ $screening->employment_verified ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} mr-2">
                            @if($screening->employment_verified)
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </span>
                        <span class="text-sm">Employment</span>
                    </div>
                    <div class="flex items-center">
                        <span class="h-5 w-5 flex items-center justify-center rounded-full {{ $screening->income_verified ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }} mr-2">
                            @if($screening->income_verified)
                                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </span>
                        <span class="text-sm">Income</span>
                    </div>
                </div>
            </div>
            
            @if($screening->notes)
                <div class="mb-4">
                    <h4 class="text-sm font-medium mb-1">Notes</h4>
                    <p class="text-gray-700 text-sm p-2 bg-gray-50 rounded">{{ $screening->notes }}</p>
                </div>
            @endif
            
            @if($role === 'landlord' && $screening->status !== 'completed')
                <div>
                    <button wire:click="editScreening" class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-xs font-medium rounded text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Screening
                    </button>
                </div>
            @endif
        </div>
    @endif
    
    @if($showForm)
        <div class="fixed inset-0 overflow-y-auto z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $isEditing ? 'Update Tenant Screening' : 'New Tenant Screening' }}</h3>
                        
                        <form wire:submit.prevent="saveScreening">
                            @if($role === 'landlord')
                                <div class="mb-4">
                                    <label for="tenantId" class="block text-sm font-medium text-gray-700 mb-1">Select Tenant</label>
                                    <select id="tenantId" wire:model="tenantId" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Select Tenant --</option>
                                        @foreach($tenantsList as $tenant)
                                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('tenantId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            @endif
                            
                            <div class="mb-4">
                                <h4 class="text-sm font-medium mb-2">Verification Checks</h4>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input id="creditCheckPassed" type="checkbox" wire:model="creditCheckPassed" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="creditCheckPassed" class="ml-2 block text-sm text-gray-700">Credit Check Passed</label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input id="backgroundCheckPassed" type="checkbox" wire:model="backgroundCheckPassed" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="backgroundCheckPassed" class="ml-2 block text-sm text-gray-700">Background Check Passed</label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input id="evictionCheckPassed" type="checkbox" wire:model="evictionCheckPassed" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="evictionCheckPassed" class="ml-2 block text-sm text-gray-700">No Eviction History</label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input id="employmentVerified" type="checkbox" wire:model="employmentVerified" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="employmentVerified" class="ml-2 block text-sm text-gray-700">Employment Verified</label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input id="incomeVerified" type="checkbox" wire:model="incomeVerified" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="incomeVerified" class="ml-2 block text-sm text-gray-700">Income Verified</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="notes" wire:model="notes" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="status" wire:model="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Save
                                </button>
                                <button type="button" wire:click="cancelForm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
