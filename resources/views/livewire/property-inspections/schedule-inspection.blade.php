<div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900">Schedule Property Inspection</h3>
            <div class="mt-4">
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

                <form wire:submit.prevent="schedule">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Unit Selection -->
                        <div class="sm:col-span-3">
                            <label for="unit_id" class="block text-sm font-medium text-gray-700">Property Unit</label>
                            <div class="mt-1">
                                <select wire:model="unit_id" id="unit_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name ?? 'Unit ' . $unit->id }}</option>
                                    @endforeach
                                </select>
                                @error('unit_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Tenant Selection -->
                        <div class="sm:col-span-3">
                            <label for="tenant_id" class="block text-sm font-medium text-gray-700">Tenant</label>
                            <div class="mt-1">
                                <select wire:model="tenant_id" id="tenant_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" {{ count($tenants) ? '' : 'disabled' }}>
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $id => $tenant)
                                        <option value="{{ $id }}">{{ $tenant->name }}</option>
                                    @endforeach
                                </select>
                                @error('tenant_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Inspection Type -->
                        <div class="sm:col-span-3">
                            <label for="type" class="block text-sm font-medium text-gray-700">Inspection Type</label>
                            <div class="mt-1">
                                <select wire:model="type" id="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    @foreach($inspectionTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Inspection Date -->
                        <div class="sm:col-span-3">
                            <label for="inspection_date" class="block text-sm font-medium text-gray-700">Inspection Date</label>
                            <div class="mt-1">
                                <input type="date" wire:model="inspection_date" id="inspection_date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                @error('inspection_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <div class="mt-1">
                                <textarea wire:model="notes" id="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Add any additional notes or instructions for this inspection.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Schedule Inspection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
