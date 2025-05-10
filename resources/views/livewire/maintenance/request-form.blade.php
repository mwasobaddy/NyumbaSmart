<div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
    <h2 class="text-lg font-medium mb-4">Report Maintenance Issue</h2>
    
    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded">
            {{ session('status') }}
        </div>
    @endif
    
    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <label for="unit_id" class="block text-sm font-medium text-gray-700">Unit</label>
            <select id="unit_id" wire:model="unit_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="">Select Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">
                        {{ $unit->property->name }} - Unit {{ $unit->unit_number }}
                    </option>
                @endforeach
            </select>
            @error('unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Issue Title</label>
            <input type="text" id="title" wire:model="title" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" wire:model="description" rows="4" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        
        <div class="flex items-center justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Submit Request
            </button>
        </div>
    </form>
</div>
