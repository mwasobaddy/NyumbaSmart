<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Manage Units</h1>

    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="{{ $unit_id ? 'update' : 'create' }}" class="mb-8 bg-white p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-1">Property</label>
                <select wire:model="property_id" class="w-full border p-2">
                    <option value="">Select Property</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1">Unit Number</label>
                <input type="text" wire:model="unit_number" class="w-full border p-2">
            </div>
            <div>
                <label class="block mb-1">Monthly Rent (KSh)</label>
                <input type="number" step="0.01" wire:model="rent" class="w-full border p-2">
            </div>
            <div>
                <label class="block mb-1">Status</label>
                <select wire:model="status" class="w-full border p-2">
                    <option value="vacant">Vacant</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Under Maintenance</option>
                </select>
            </div>
            <div class="flex items-center">
                <input type="checkbox" wire:model="manual_water" id="manual_water" class="mr-2">
                <label for="manual_water">Manual Water Billing</label>
            </div>
            <div class="flex items-center">
                <input type="checkbox" wire:model="manual_electricity" id="manual_electricity" class="mr-2">
                <label for="manual_electricity">Manual Electricity Billing</label>
            </div>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            {{ $unit_id ? 'Update Unit' : 'Add New Unit' }}
        </button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-left">Property</th>
                    <th class="border p-2 text-left">Unit #</th>
                    <th class="border p-2 text-right">Rent</th>
                    <th class="border p-2 text-center">Status</th>
                    <th class="border p-2 text-center">Manual Bills</th>
                    <th class="border p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($units as $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-2">{{ $unit->property->name }}</td>
                        <td class="border p-2">{{ $unit->unit_number }}</td>
                        <td class="border p-2 text-right">KSh {{ number_format($unit->rent, 2) }}</td>
                        <td class="border p-2 text-center">
                            <span class="px-2 py-1 rounded text-xs 
                                {{ $unit->status === 'vacant' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $unit->status === 'occupied' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $unit->status === 'maintenance' ? 'bg-orange-100 text-orange-800' : '' }}
                            ">
                                {{ ucfirst($unit->status) }}
                            </span>
                        </td>
                        <td class="border p-2 text-center">
                            @if($unit->manual_water) <span class="text-xs bg-blue-100 text-blue-800 px-1 rounded">Water</span> @endif
                            @if($unit->manual_electricity) <span class="text-xs bg-yellow-100 text-yellow-800 px-1 rounded">Electricity</span> @endif
                        </td>
                        <td class="border p-2 text-center">
                            <button wire:click="edit({{ $unit->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs">Edit</button>
                            <button wire:click="delete({{ $unit->id }})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs ml-1">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
