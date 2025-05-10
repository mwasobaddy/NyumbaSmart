<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-6">{{ $is_landlord ? 'Property Maintenance Requests' : 'Report Maintenance Issues' }}</h1>
    
    @if(session('status'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            {{ session('status') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Create/Edit Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">
            {{ $maintenance_request_id ? 'Update Request' : ($is_landlord ? 'Process Request' : 'Report an Issue') }}
        </h2>
        
        <form wire:submit.prevent="{{ $maintenance_request_id ? 'update' : 'create' }}">
            @if(!$is_landlord || !$maintenance_request_id)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" wire:model="title" class="w-full border-gray-300 rounded-md shadow-sm" 
                            placeholder="Brief title for the issue">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <select wire:model="unit_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->property->name }} - Unit {{ $unit->unit_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="4" class="w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="Please describe the issue in detail"></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            @endif
            
            @if($is_landlord && $maintenance_request_id)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Budget Estimate (KSh)</label>
                        <input type="number" wire:model="budget_estimate" step="0.01" min="0"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('budget_estimate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                    <textarea wire:model="admin_notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="Private notes about this maintenance request"></textarea>
                    @error('admin_notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            @endif
            
            <div class="flex justify-end gap-2">
                @if($maintenance_request_id)
                    <button type="button" wire:click="resetInput" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Cancel
                    </button>
                @endif
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                    {{ $maintenance_request_id ? 'Update' : 'Submit' }}
                </button>
            </div>
        </form>
    </div>
    
    <!-- Requests List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">{{ $is_landlord ? 'All Maintenance Requests' : 'Your Maintenance Requests' }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property/Unit</th>
                        @if($is_landlord)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported By</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $request->unit->property->name }} - Unit {{ $request->unit->unit_number }}
                            </td>
                            @if($is_landlord)
                                <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->name }}</td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $request->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $request->id }})" class="text-blue-600 hover:text-blue-900">
                                    {{ $is_landlord ? 'Process' : 'Edit' }}
                                </button>
                                
                                @if($request->status === 'pending')
                                    <button wire:click="delete({{ $request->id }})" class="ml-3 text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $is_landlord ? '6' : '5' }}" class="px-6 py-4 text-center text-gray-500">
                                No maintenance requests found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
