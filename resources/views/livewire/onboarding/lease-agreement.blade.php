<div class="w-full p-4">
    @if(!$unitId)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Please select a unit to manage lease agreements.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Lease Agreement Management</h2>
            
            @if($role === 'landlord' && (!$agreement || $agreement->status === 'draft' || $agreement->status === 'terminated'))
                <button wire:click="createAgreement" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    {{ $agreement ? 'Create New Agreement' : 'Create Agreement' }}
                </button>
            @endif
        </div>
        
        @if($showForm)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-medium mb-4">{{ $isEditing ? 'Edit' : 'Create' }} Lease Agreement</h3>
                
                <form wire:submit.prevent="previewAgreement" class="space-y-4">
                    @if($role === 'landlord')
                        <div>
                            <label for="tenantId" class="block text-sm font-medium text-gray-700">Tenant</label>
                            <select wire:model="tenantId" id="tenantId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">Select a tenant</option>
                                @foreach($tenantsList as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->email }})</option>
                                @endforeach
                            </select>
                            @error('tenantId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="startDate" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" wire:model="startDate" id="startDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            @error('startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" wire:model="endDate" id="endDate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            @error('endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="rentAmount" class="block text-sm font-medium text-gray-700">Monthly Rent (KES)</label>
                            <input type="number" wire:model="rentAmount" id="rentAmount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            @error('rentAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="securityDeposit" class="block text-sm font-medium text-gray-700">Security Deposit (KES)</label>
                            <input type="number" wire:model="securityDeposit" id="securityDeposit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            @error('securityDeposit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea wire:model="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Agreement Text</label>
                        <div class="mt-1 flex justify-end">
                            <button type="button" wire:click="generateAgreementText" class="text-sm text-blue-600 hover:text-blue-800">
                                Generate Template Text
                            </button>
                        </div>
                        <textarea wire:model="agreementText" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 font-mono text-sm"></textarea>
                        @error('agreementText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="cancelForm" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Preview Agreement
                        </button>
                    </div>
                </form>
            </div>
        @elseif($showPreview)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-medium mb-4">Agreement Preview</h3>
                
                <div class="border rounded-md p-4 mb-4 bg-gray-50">
                    <pre class="whitespace-pre-wrap font-mono text-sm">{{ $agreementText }}</pre>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button wire:click="cancelForm" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="saveAgreement" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Save Agreement
                    </button>
                </div>
            </div>
        @elseif($agreement)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-medium">Current Lease Agreement</h3>
                        <p class="text-sm text-gray-500">Last updated: {{ $agreement->updated_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($agreement->status === 'draft') bg-gray-100 text-gray-800
                        @elseif($agreement->status === 'pending_tenant') bg-yellow-100 text-yellow-800
                        @elseif($agreement->status === 'pending_landlord') bg-blue-100 text-blue-800
                        @elseif($agreement->status === 'signed') bg-purple-100 text-purple-800
                        @elseif($agreement->status === 'active') bg-green-100 text-green-800
                        @elseif($agreement->status === 'terminated') bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $agreement->status)) }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Property</h4>
                        <p>{{ $unit->property->name }}, Unit {{ $unit->unit_number }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Term</h4>
                        <p>{{ $agreement->start_date->format('M d, Y') }} to {{ $agreement->end_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Monthly Rent</h4>
                        <p>KES {{ number_format($agreement->rent_amount) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Security Deposit</h4>
                        <p>KES {{ number_format($agreement->security_deposit) }}</p>
                    </div>
                </div>
                
                @if($agreement->notes)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-500">Additional Notes</h4>
                        <p class="text-sm">{{ $agreement->notes }}</p>
                    </div>
                @endif
                
                <div class="border rounded-md p-4 mb-4 bg-gray-50 max-h-80 overflow-y-auto">
                    <pre class="whitespace-pre-wrap font-mono text-sm">{{ $agreement->agreement_text }}</pre>
                </div>
                
                <div class="flex justify-end space-x-3">
                    @if($role === 'landlord' && in_array($agreement->status, ['draft', 'pending_landlord', 'signed']))
                        @if($agreement->status === 'draft')
                            <button wire:click="signAgreement" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Send to Tenant
                            </button>
                        @elseif($agreement->status === 'pending_landlord')
                            <button wire:click="signAgreement" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Sign Agreement
                            </button>
                        @elseif($agreement->status === 'signed')
                            <button wire:click="activateAgreement" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Activate Agreement
                            </button>
                        @endif
                    @endif
                    
                    @if(($role === 'tenant' && $agreement->status === 'pending_tenant') || 
                        ($role === 'landlord' && in_array($agreement->status, ['draft'])))
                        <button wire:click="signAgreement" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            {{ $role === 'tenant' ? 'Sign Agreement' : 'Send to Tenant' }}
                        </button>
                    @endif
                    
                    @if($role === 'landlord' && in_array($agreement->status, ['draft']))
                        <button wire:click="editAgreement" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Edit
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No agreement</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $role === 'landlord' ? 'Create a new lease agreement for this unit.' : 'No lease agreement has been created for you yet.' }}
                    </p>
                </div>
            </div>
        @endif
    @endif
</div>
