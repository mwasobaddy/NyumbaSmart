<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-6">{{ $is_landlord ? 'Vacate Notices' : 'Submit Vacate Notice' }}</h1>
    
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
    
    @if(!$is_landlord)
    <!-- Tenant Form to Submit Notice -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Notice of Intent to Vacate</h2>
        
        <form wire:submit.prevent="create">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Unit</label>
                    <select wire:model="unit_id" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Your Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->property->name }} - Unit {{ $unit->unit_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Planned Move-out Date</label>
                    <input type="date" wire:model="move_out_date" class="w-full border-gray-300 rounded-md shadow-sm">
                    @error('move_out_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <small class="text-gray-500">Must be at least 30 days from today</small>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason (Optional)</label>
                <textarea wire:model="reason" rows="3" class="w-full border-gray-300 rounded-md shadow-sm"
                    placeholder="Reason for vacating this unit"></textarea>
                @error('reason') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                    Submit Notice
                </button>
            </div>
        </form>
    </div>
    @endif
    
    <!-- Notices List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">{{ $is_landlord ? 'Tenant Vacate Notices' : 'Your Submitted Notices' }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if($is_landlord)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property/Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notice Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Move Out Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Left</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($notices as $notice)
                        <tr>
                            @if($is_landlord)
                                <td class="px-6 py-4 whitespace-nowrap">{{ $notice->user->name }}</td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $notice->unit->property->name }} - Unit {{ $notice->unit->unit_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($notice->notice_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($notice->move_out_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $notice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($notice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php 
                                    $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($notice->move_out_date), false);
                                @endphp
                                <span class="{{ $daysLeft < 7 ? 'text-red-600 font-bold' : '' }}">
                                    {{ $daysLeft > 0 ? $daysLeft : 'Move out day' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($is_landlord)
                                    @if($notice->status === 'pending')
                                        <button wire:click="processNotice({{ $notice->id }}, 'processed')" class="text-blue-600 hover:text-blue-900">
                                            Mark Processed
                                        </button>
                                    @else
                                        <button wire:click="processNotice({{ $notice->id }}, 'pending')" class="text-yellow-600 hover:text-yellow-900">
                                            Mark Pending
                                        </button>
                                    @endif
                                @else
                                    @if($notice->status === 'pending')
                                        <button wire:click="delete({{ $notice->id }})" class="text-red-600 hover:text-red-900">
                                            Withdraw
                                        </button>
                                    @else
                                        <span class="text-gray-500">Confirmed</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $is_landlord ? 7 : 6 }}" class="px-6 py-4 text-center text-gray-500">
                                No vacate notices found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
