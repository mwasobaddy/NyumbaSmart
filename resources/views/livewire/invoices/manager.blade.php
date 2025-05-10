<div class="container mx-auto py-6">
    <h1 class="text-2xl font-bold mb-6">{{ $is_landlord ? 'Manage Invoices' : 'My Invoices' }}</h1>
    
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
        <!-- Landlord View - Create/Edit Invoices -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4">{{ $invoice_id ? 'Edit Invoice' : 'Create New Invoice' }}</h2>
            
            <form wire:submit.prevent="{{ $invoice_id ? 'updateInvoice' : 'createInvoice' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                        <select wire:model="selectedProperty" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select Property</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <select wire:model="unit_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">Unit {{ $unit->unit_number }} ({{ $unit->status }})</option>
                            @endforeach
                        </select>
                        @error('unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (KSh)</label>
                        <input type="number" wire:model="amount" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" wire:model="due_date" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Late Payment Penalty (KSh)</label>
                        <input type="number" wire:model="penalty" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('penalty') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Type</label>
                        <select wire:model="invoice_type" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="rent">Rent</option>
                            <option value="water">Water</option>
                            <option value="electricity">Electricity</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                    <textarea wire:model="additional_notes" rows="2" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                
                <div class="flex justify-end gap-2">
                    @if($invoice_id)
                        <button type="button" wire:click="resetForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Cancel
                        </button>
                    @endif
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                        {{ $invoice_id ? 'Update Invoice' : 'Create Invoice' }}
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Filter Controls -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-4">
            <div class="flex gap-4 items-center">
                <div class="w-64">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Filter by Property</label>
                    <select wire:model="selectedProperty" wire:change="loadInvoices" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">All Properties</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @else
        <!-- Payment Modal (for tenants) -->
        @if($payment_invoice_id)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 z-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-semibold mb-4">Pay Invoice</h3>
                    
                    <form wire:submit.prevent="processPayment">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount to Pay</label>
                            <div class="text-lg font-bold">KSh {{ number_format($amount, 2) }}</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select wire:model="payment_method" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="mpesa">M-Pesa</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" wire:model="phone" placeholder="07xxxxxxxx" class="w-full border-gray-300 rounded-md shadow-sm">
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-1">You will receive an STK push on this number</p>
                        </div>
                        
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="resetPaymentForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                                Pay Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
    
    <!-- Invoices List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">{{ $is_landlord ? 'All Invoices' : 'Your Invoices' }}</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                        @if($is_landlord)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property/Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">INV-{{ $invoice->id }}</td>
                            @if($is_landlord)
                                <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->user->name }}</td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $invoice->unit->property->name }} - Unit {{ $invoice->unit->unit_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                KSh {{ number_format($invoice->amount, 2) }}
                                @if($invoice->penalty > 0)
                                    <br><span class="text-xs text-red-600">+{{ number_format($invoice->penalty, 2) }} penalty</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                @php
                                    $now = Carbon\Carbon::now();
                                    $dueDate = Carbon\Carbon::parse($invoice->due_date);
                                    $daysUntilDue = $now->diffInDays($dueDate, false);
                                @endphp
                                
                                @if($invoice->status == 'pending' && $daysUntilDue < 0)
                                    <br><span class="text-xs text-red-600">{{ abs($daysUntilDue) }} days overdue</span>
                                @elseif($invoice->status == 'pending' && $daysUntilDue <= 3)
                                    <br><span class="text-xs text-orange-600">Due {{ $daysUntilDue == 0 ? 'today' : 'in ' . $daysUntilDue . ' days' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $invoice->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($is_landlord)
                                    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                                        <button wire:click="editInvoice({{ $invoice->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            Edit
                                        </button>
                                        <button wire:click="cancelInvoice({{ $invoice->id }})" class="text-red-600 hover:text-red-900">
                                            Cancel
                                        </button>
                                    @endif
                                @else
                                    @if($invoice->status === 'pending' || $invoice->status === 'overdue')
                                        @livewire('payments.mpesa-payment', ['invoiceId' => $invoice->id], key('mpesa-payment-' . $invoice->id))
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $is_landlord ? 7 : 6 }}" class="px-6 py-4 text-center text-gray-500">
                                No invoices found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
