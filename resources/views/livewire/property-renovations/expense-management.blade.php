<div>
    @if(!$renovation)
        <div class="flex items-center justify-center h-64">
            <div class="text-center">
                <div class="text-gray-500">Loading renovation expenses...</div>
                <div class="mt-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                </div>
            </div>
        </div>
    @else
        <div>
            @if(session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Budget Summary -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Budget Summary</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $renovation->title }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Budget</p>
                        <p class="text-xl font-semibold text-gray-900">KES {{ number_format($renovation->budget) }}</p>
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    <div class="px-4 py-4 sm:px-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Total Spent</p>
                            <p class="text-lg font-medium {{ $renovation->is_over_budget ? 'text-red-600' : 'text-gray-900' }}">
                                KES {{ number_format($renovation->actual_cost) }}
                            </p>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, ($renovation->budget > 0 ? ($renovation->actual_cost / $renovation->budget) * 100 : 0)) }}%"></div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ $renovation->budget > 0 ? round(($renovation->actual_cost / $renovation->budget) * 100) : 0 }}% of budget</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Remaining</p>
                            <p class="text-lg font-medium {{ ($renovation->budget - $renovation->actual_cost) < 0 ? 'text-red-600' : 'text-green-600' }}">
                                KES {{ number_format(max(0, $renovation->budget - $renovation->actual_cost)) }}
                            </p>
                            @if(($renovation->budget - $renovation->actual_cost) < 0)
                                <p class="text-sm text-red-600">
                                    Over budget by KES {{ number_format(abs($renovation->budget - $renovation->actual_cost)) }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Expenses Count</p>
                            <p class="text-lg font-medium text-gray-900">{{ $expenses->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Expense Management -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Expense Management</h3>
                    @if(!$isAddingExpense)
                        <button type="button" wire:click="showAddExpenseForm" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Expense
                        </button>
                    @endif
                </div>
                
                @if($isAddingExpense)
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <form wire:submit.prevent="saveExpense">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="expense_title" class="block text-sm font-medium text-gray-700">Expense Title *</label>
                                    <input type="text" name="expense_title" id="expense_title" wire:model="expense.title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('expense.title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="expense_vendor_id" class="block text-sm font-medium text-gray-700">Vendor</label>
                                    <select id="expense_vendor_id" name="expense_vendor_id" wire:model="expense.vendor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">-- Select Vendor (Optional) --</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="expense_amount" class="block text-sm font-medium text-gray-700">Amount (KES) *</label>
                                    <input type="number" name="expense_amount" id="expense_amount" step="0.01" min="0" wire:model="expense.amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('expense.amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="expense_date" class="block text-sm font-medium text-gray-700">Date *</label>
                                    <input type="date" name="expense_date" id="expense_date" wire:model="expense.date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('expense.date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="expense_category" class="block text-sm font-medium text-gray-700">Category *</label>
                                    <select id="expense_category" name="expense_category" wire:model="expense.category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">-- Select Category --</option>
                                        @foreach($expenseCategories as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('expense.category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="sm:col-span-3">
                                    <label for="expense_receipt" class="block text-sm font-medium text-gray-700">Receipt (Optional)</label>
                                    <input type="file" name="expense_receipt" id="expense_receipt" wire:model="receiptFile" class="mt-1 block w-full border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <div wire:loading wire:target="receiptFile" class="text-sm text-blue-500 mt-1">Uploading...</div>
                                    @error('receiptFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <label for="expense_description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="expense_description" name="expense_description" wire:model="expense.description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                            
                            <div class="mt-5 flex justify-end space-x-3">
                                <button type="button" wire:click="cancelAddExpense" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Save Expense
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
                
                <div class="{{ $isAddingExpense ? 'border-t border-gray-200' : '' }}">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Title
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Category
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Vendor
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Amount
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Receipt
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($expenses as $index => $expense)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $expense->date->format('M j, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $expense->title }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            @if($expense->category === 'materials') bg-green-100 text-green-800
                                                            @elseif($expense->category === 'labor') bg-blue-100 text-blue-800
                                                            @elseif($expense->category === 'equipment') bg-purple-100 text-purple-800
                                                            @else bg-yellow-100 text-yellow-800 @endif">
                                                            {{ $expenseCategories[$expense->category] ?? 'Other' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $expense->vendor ? $expense->vendor->name : '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        KES {{ number_format($expense->amount) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        @if($expense->receipt_path)
                                                            <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                                                View Receipt
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <button wire:click="editExpense({{ $expense->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                            Edit
                                                        </button>
                                                        <button wire:click="confirmDeleteExpense({{ $expense->id }})" class="text-red-600 hover:text-red-900">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        No expenses recorded for this renovation yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delete Confirmation Modal -->
            @if($showDeleteModal)
                <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Delete Expense
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                Are you sure you want to delete this expense? This action cannot be undone.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" wire:click="deleteExpense" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Delete
                                </button>
                                <button type="button" wire:click="cancelDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
