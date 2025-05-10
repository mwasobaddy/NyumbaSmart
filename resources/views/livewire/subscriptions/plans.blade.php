<div>
    @if (session()->has('status'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach($plans as $plan)
            <div class="border rounded p-4 flex flex-col">
                <h2 class="text-xl font-semibold mb-2">{{ $plan->name }}</h2>
                <p class="text-gray-600 mb-4">{{ number_format($plan->price, 2) }} KES / {{ $plan->duration_months }} mo</p>

                <form wire:submit.prevent="checkout">
                    <input type="hidden" wire:model="plan_id" value="{{ $plan->id }}">

                    <label class="block text-sm font-medium text-gray-700">Phone (254xx...):</label>
                    <input type="text" wire:model="phone" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="2547XXXXXXXX" required>
                    @error('phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

                    <button type="submit" class="mt-4 bg-blue-600 text-white py-2 px-4 rounded">
                        Subscribe
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
