<div>
    @if(!$renovation)
        <div class="flex items-center justify-center h-64">
            <div class="text-center">
                <div class="text-gray-500">Loading renovation details...</div>
                <div class="mt-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if(session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif
            
            <!-- Header Section -->
            <div class="border-b border-gray-200 px-6 py-5 flex justify-between items-start">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $renovation->title }}
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        @if($renovation->unit)
                            {{ $renovation->property->name }} - Unit {{ $renovation->unit->unit_number }}
                        @else
                            {{ $renovation->property->name }} - Entire Property
                        @endif
                    </p>
                </div>
                
                <div>
                    @if(!$isEditing)
                        <button wire:click="enableEditing" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Edit Details
                        </button>
                    @endif
                </div>
            </div>
            
            @if($isEditing)
                <!-- Edit Form -->
                <form wire:submit.prevent="updateRenovation" class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                                <input type="text" id="title" wire:model="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                                <input type="date" id="start_date" wire:model="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                                <input type="date" id="end_date" wire:model="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label for="budget" class="block text-sm font-medium text-gray-700">Budget (KES) *</label>
                                <input type="number" id="budget" wire:model="budget" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('budget') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select id="status" wire:model="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                <textarea id="notes" wire:model="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="documents" class="block text-sm font-medium text-gray-700">Upload Documents</label>
                                <input type="file" id="documents" wire:model="documents" multiple class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <div wire:loading wire:target="documents" class="text-sm text-blue-500 mt-1">Uploading...</div>
                                @error('documents.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <p class="mt-1 text-xs text-gray-500">Upload project documents (max 10MB each)</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="cancelEditing" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Changes
                        </button>
                    </div>
                </form>
            @else
                <!-- Details View -->
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900">Overview</h4>
                                <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($renovation->status === 'planned') bg-blue-100 text-blue-800 
                                                @elseif($renovation->status === 'in_progress') bg-yellow-100 text-yellow-800 
                                                @elseif($renovation->status === 'completed') bg-green-100 text-green-800 
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ $statusOptions[$renovation->status] }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $renovation->start_date->format('M j, Y') }}</dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $renovation->end_date->format('M j, Y') }}</dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $renovation->start_date->diffInDays($renovation->end_date) + 1 }} days</dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900">Description</h4>
                                <div class="mt-2 text-sm text-gray-600">
                                    {{ $renovation->description ?: 'No description provided.' }}
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="font-medium text-gray-900">Notes</h4>
                                <div class="mt-2 text-sm text-gray-600">
                                    {{ $renovation->notes ?: 'No additional notes.' }}
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900">Budget Information</h4>
                                <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Budget</dt>
                                        <dd class="mt-1 text-sm text-gray-900">KES {{ number_format($renovation->budget) }}</dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Actual Cost</dt>
                                        <dd class="mt-1 text-sm {{ $renovation->is_over_budget ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            KES {{ number_format($renovation->actual_cost) }}
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Remaining Budget</dt>
                                        <dd class="mt-1 text-sm {{ ($renovation->budget - $renovation->actual_cost) < 0 ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            KES {{ number_format(max(0, $renovation->budget - $renovation->actual_cost)) }}
                                        </dd>
                                    </div>
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Budget Utilization</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $renovation->budget > 0 ? round(($renovation->actual_cost / $renovation->budget) * 100) : 0 }}%
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            
                            <div class="mt-6">
                                <h4 class="font-medium text-gray-900">Documents</h4>
                                <ul class="mt-2 border border-gray-200 rounded-md divide-y divide-gray-200">
                                    @forelse($existingDocuments as $index => $document)
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2 flex-1 w-0 truncate">
                                                    {{ $document['name'] }}
                                                </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0 flex space-x-4">
                                                <a href="{{ Storage::url($document['path']) }}" target="_blank" class="font-medium text-blue-600 hover:text-blue-500">
                                                    View
                                                </a>
                                                <button type="button" wire:click="removeDocument({{ $index }})" class="font-medium text-red-600 hover:text-red-500">
                                                    Remove
                                                </button>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="pl-3 pr-4 py-3 text-sm text-gray-500">
                                            No documents uploaded.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
