<div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            @if(!$inspection)
                <div class="text-center py-10">
                    <p>No inspection selected. Please select an inspection to view or create.</p>
                </div>
            @else
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Inspection Report for {{ $unitName }}
                    </h3>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status === 'completed' ? 'bg-green-100 text-green-800' : ($status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($inspection->type) }}
                        </span>
                    </div>
                </div>
                
                @if (session()->has('message'))
                    <div class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Signatures Status -->
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <div class="flex justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-700">Landlord Signature:</h4>
                            <p class="text-sm text-gray-600">
                                @if($inspection->landlord_signed)
                                    Signed on {{ $inspection->landlord_signed_at->format('M d, Y \a\t h:i A') }}
                                @else
                                    Not signed
                                @endif
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-700">Tenant Signature:</h4>
                            <p class="text-sm text-gray-600">
                                @if($inspection->tenant_signed)
                                    Signed on {{ $inspection->tenant_signed_at->format('M d, Y \a\t h:i A') }}
                                @else
                                    Not signed
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Inspection Form -->
                <form wire:submit.prevent="saveReport">
                    <div class="space-y-6">
                        <!-- Inspection Checklist -->
                        <div>
                            <h3 class="text-base font-medium text-gray-900">Inspection Checklist</h3>
                            <p class="mt-1 text-sm text-gray-500">Document the condition of each item in the property.</p>
                            
                            <div class="mt-2">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Item</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Condition</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Notes</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($checklist_items as $index => $item)
                                            <tr>
                                                <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm">
                                                    <input type="text" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                                                        wire:model="checklist_items.{{ $index }}.name">
                                                </td>
                                                <td class="px-3 py-2 text-sm">
                                                    <select class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                        wire:model="checklist_items.{{ $index }}.condition">
                                                        <option value="">Select condition</option>
                                                        @foreach($conditionOptions as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-3 py-2 text-sm">
                                                    <input type="text" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                                                        wire:model="checklist_items.{{ $index }}.notes">
                                                </td>
                                                <td class="px-3 py-2 text-sm text-right">
                                                    <button type="button" wire:click="removeChecklistItem({{ $index }})" class="text-red-600 hover:text-red-900">
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="mt-2">
                                    <button type="button" wire:click="addChecklistItem" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Add Item
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Overall Condition -->
                        <div>
                            <label for="overall_condition" class="block text-sm font-medium text-gray-700">Overall Condition</label>
                            <div class="mt-1">
                                <select wire:model="overall_condition" id="overall_condition" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select overall condition</option>
                                    @foreach($conditionOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('overall_condition') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <div class="mt-1">
                                <textarea wire:model="notes" id="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <!-- Status Controls -->
                        <div class="flex space-x-3">
                            <button type="button" wire:click="$set('status', 'scheduled')" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 {{ $status === 'scheduled' ? 'bg-gray-100' : '' }}">
                                Scheduled
                            </button>
                            <button type="button" wire:click="$set('status', 'in_progress')" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 {{ $status === 'in_progress' ? 'bg-gray-100' : '' }}">
                                In Progress
                            </button>
                            <button type="button" wire:click="completeInspection" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 {{ $status === 'completed' ? 'opacity-75 cursor-not-allowed' : '' }}" {{ $status === 'completed' ? 'disabled' : '' }}>
                                Mark as Completed
                            </button>
                        </div>

                        <!-- Save Button -->
                        <div class="flex space-x-3">
                            @if($status === 'completed' && !$inspection->isFullySigned())
                                <button type="button" wire:click="signInspection" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ auth()->user()->id === $inspection->tenant_id ? 'Sign as Tenant' : 'Sign as Landlord' }}
                                </button>
                            @endif
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Save Report
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
