<div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            @if(!$inspection)
                <div class="text-center py-10">
                    <p>No inspection selected. Please select an inspection to view or upload photos.</p>
                </div>
            @else
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Photo Documentation
                    </h3>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : ($inspection->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($inspection->type) }} Inspection
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

                @if (session()->has('info'))
                    <div class="rounded-md bg-blue-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Upload Photos Form -->
                <div class="mb-8">
                    <h4 class="text-base font-medium text-gray-900 mb-2">Upload Photos</h4>
                    <form wire:submit.prevent="savePhotos">
                        <div class="flex items-center">
                            <div class="flex-grow">
                                <label for="photos" class="sr-only">Choose photos</label>
                                <input type="file" wire:model="photos" id="photos" multiple accept="image/*" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-medium
                                    file:bg-primary-50 file:text-primary-700
                                    hover:file:bg-primary-100">
                                @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="ml-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Upload
                                </button>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Upload up to 5 photos at a time. Maximum file size: 5MB per photo.</p>
                    </form>
                </div>

                <!-- Existing Photos -->
                <div>
                    <h4 class="text-base font-medium text-gray-900 mb-2">Inspection Photos</h4>
                    
                    @if(empty($existingPhotos))
                        <div class="text-center py-6 bg-gray-50 rounded-md border border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No photos uploaded yet.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($existingPhotos as $index => $photo)
                                <div class="border border-gray-200 rounded-md overflow-hidden">
                                    <div class="h-48 bg-gray-200 overflow-hidden">
                                        <img src="{{ Storage::url($photo['path']) }}" alt="Inspection photo" class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-3">
                                        <div class="mb-2">
                                            <div class="text-xs text-gray-500">
                                                {{ isset($photo['uploaded_at']) ? \Carbon\Carbon::parse($photo['uploaded_at'])->format('M d, Y \a\t h:i A') : 'Date unknown' }}
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="photo-desc-{{ $index }}" class="sr-only">Photo description</label>
                                            <input type="text" id="photo-desc-{{ $index }}" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                placeholder="Add description"
                                                wire:model="photoDescriptions.{{ $photo['path'] }}"
                                                wire:change="updateDescription({{ $index }}, $event.target.value)">
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="button" wire:click="removePhoto({{ $index }})" class="text-sm text-red-600 hover:text-red-900">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
