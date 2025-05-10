<div>
    @if(!$showPaymentForm)
        <button 
            wire:click="togglePaymentForm" 
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
            Pay with M-Pesa
        </button>
    @else
        <div class="bg-white rounded-lg shadow-md p-6 max-w-lg mx-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">M-Pesa Payment</h3>
                <button wire:click="togglePaymentForm" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            @if($showStatusMessage)
                <div class="mb-4 p-3 rounded-md 
                    {{ $status == 'success' ? 'bg-green-100 text-green-800' : 
                       ($status == 'processing' ? 'bg-yellow-100 text-yellow-800' : 
                       ($status == 'error' ? 'bg-red-100 text-red-800' : 
                        'bg-gray-100 text-gray-800')) }}">
                    {{ $statusMessage }}
                </div>
            @endif
            
            @if(!$showPaymentResult)
                <form wire:submit.prevent="initiatePayment" class="space-y-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" id="phone" wire:model="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g. 0712345678">
                        @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount (KES)</label>
                        <input type="number" id="amount" wire:model="amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @error('amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Pay Now
                        </button>
                    </div>
                </form>
            @else
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Payment Simulation</h4>
                    <p class="text-sm text-gray-500 mb-4">For testing purposes, please simulate the payment result:</p>
                    
                    <div class="flex space-x-4">
                        <button wire:click="simulatePaymentSuccess" class="py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Simulate Success
                        </button>
                        <button wire:click="simulatePaymentFailure" class="py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Simulate Fail
                        </button>
                    </div>
                </div>
                
                @if($receipt)
                    <div class="mt-6 p-4 border rounded-md bg-gray-50">
                        <h4 class="font-semibold text-lg mb-2">Payment Receipt</h4>
                        <div class="space-y-1 text-sm">
                            <div><strong>Receipt Number:</strong> {{ $receipt['receipt_number'] }}</div>
                            <div><strong>Amount:</strong> KES {{ $receipt['amount'] }}</div>
                            <div><strong>Phone Number:</strong> {{ $receipt['phone'] }}</div>
                            <div><strong>Reference:</strong> {{ $receipt['reference'] }}</div>
                            <div><strong>Description:</strong> {{ $receipt['description'] }}</div>
                            <div><strong>Date:</strong> {{ $receipt['date'] }}</div>
                        </div>
                        <div class="mt-4">
                            <button class="py-1 px-3 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700" onclick="window.print()">
                                Print Receipt
                            </button>
                        </div>
                    </div>
                @endif
                
                <div class="mt-4 text-right">
                    <button wire:click="resetComponent" class="text-sm text-indigo-600 hover:text-indigo-900">
                        Make Another Payment
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
