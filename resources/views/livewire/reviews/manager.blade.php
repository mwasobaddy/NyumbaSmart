<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-6">{{ $is_landlord ? 'Property Reviews' : 'Leave a Review' }}</h1>
    
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
    
    @if($is_landlord)
        <!-- Landlord View - Filter Reviews -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4">Filter Reviews</h2>
            <div class="flex gap-4">
                <div class="w-64">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                    <select wire:model="property_id" wire:change="filterByProperty" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">All Properties</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @else
        <!-- Tenant View - Submit Review Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4">{{ $review_id ? 'Edit Review' : 'Submit a New Review' }}</h2>
            
            <form wire:submit.prevent="{{ $review_id ? 'update' : 'create' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Property/Unit</label>
                        <select wire:model="unit_id" class="w-full border-gray-300 rounded-md shadow-sm" {{ $review_id ? 'disabled' : '' }}>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('rating', {{ $i }})" class="text-2xl focus:outline-none">
                                    @if($i <= $rating)
                                        <span class="text-yellow-400">★</span>
                                    @else
                                        <span class="text-gray-300">★</span>
                                    @endif
                                </button>
                            @endfor
                        </div>
                        @error('rating') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Your Review</label>
                    <textarea wire:model="comment" rows="4" class="w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="Please share your experience with this property"></textarea>
                    @error('comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex justify-end gap-2">
                    @if($review_id)
                        <button type="button" wire:click="resetInput" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Cancel
                        </button>
                    @endif
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                        {{ $review_id ? 'Update Review' : 'Submit Review' }}
                    </button>
                </div>
            </form>
        </div>
    @endif
    
    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">
                {{ $is_landlord ? 'Reviews for Your Properties' : 'Your Reviews' }}
            </h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($reviews as $review)
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold">
                                {{ $review->unit->property->name }} - Unit {{ $review->unit->unit_number }}
                            </h4>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <span>★</span>
                                        @else
                                            <span class="text-gray-300">★</span>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm text-gray-600">
                                    {{ $review->created_at->format('M d, Y') }}
                                    @if($is_landlord)
                                        by {{ $review->user->name }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        @if(!$is_landlord && $review->user_id === Auth::id())
                            <div class="flex">
                                <button wire:click="edit({{ $review->id }})" class="text-sm text-blue-600 mr-4">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $review->id }})" class="text-sm text-red-600">
                                    Delete
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <p class="mt-3 text-gray-700">
                        {{ $review->comment }}
                    </p>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">
                    No reviews found
                </div>
            @endforelse
        </div>
    </div>
</div>
