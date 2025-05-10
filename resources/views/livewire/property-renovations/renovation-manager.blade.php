<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Property Renovation Tracking</h1>
        
        @if($activeTab === 'list')
            <button wire:click="showCreateForm" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Schedule Renovation
            </button>
        @else
            <button wire:click="setActiveTab('list')" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </button>
        @endif
    </div>
    
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    <div>
        @if($activeTab === 'list')
            <!-- Renovation List Tab -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Renovation Projects</h3>
                    
                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" wire:model.live="search" placeholder="Search renovations..." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                            <select wire:model.live="propertyFilter" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">All Properties</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                            <select wire:model.live="dateRange" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">All Dates</option>
                                <option value="current">Currently Active</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($renovations as $renovation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $renovation->title }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($renovation->unit)
                                                Unit: {{ $renovation->unit->unit_number }}
                                            @else
                                                Entire Property
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $renovation->property->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Start: {{ $renovation->start_date->format('M j, Y') }}</div>
                                        <div class="text-sm text-gray-500">End: {{ $renovation->end_date->format('M j, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Budget: KES {{ number_format($renovation->budget) }}</div>
                                        <div class="text-sm {{ $renovation->is_over_budget ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                            Spent: KES {{ number_format($renovation->actual_cost) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($renovation->status === 'planned') bg-blue-100 text-blue-800 
                                            @elseif($renovation->status === 'in_progress') bg-yellow-100 text-yellow-800 
                                            @elseif($renovation->status === 'completed') bg-green-100 text-green-800 
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $statusOptions[$renovation->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $renovation->progress_percentage }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ round($renovation->progress_percentage) }}% complete</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="selectRenovation({{ $renovation->id }})" class="text-blue-600 hover:text-blue-900 mr-2">
                                            View Details
                                        </button>
                                        <button wire:click="showVendors({{ $renovation->id }})" class="text-green-600 hover:text-green-900 mr-2">
                                            Vendors ({{ $renovation->vendors->count() }})
                                        </button>
                                        <button wire:click="showExpenses({{ $renovation->id }})" class="text-purple-600 hover:text-purple-900">
                                            Expenses
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-500">No renovation projects found.</div>
                                        <button wire:click="showCreateForm" class="mt-2 text-blue-600 hover:text-blue-900">
                                            Create your first renovation project
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $renovations->links() }}
                </div>
            </div>
        @elseif($activeTab === 'create')
            <!-- Create Renovation Tab -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Schedule New Renovation</h3>
                    <p class="mt-1 text-sm text-gray-500">Fill out the form below to create a new renovation project.</p>
                </div>
                
                <div class="p-4">
                    <livewire:property-renovations.create-renovation />
                </div>
            </div>
        @elseif($activeTab === 'details' && $selectedRenovation)
            <!-- Renovation Details Tab -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Renovation Details</h3>
                </div>
                
                <div class="p-4">
                    <livewire:property-renovations.renovation-details :renovationId="$selectedRenovationId" />
                </div>
            </div>
        @elseif($activeTab === 'vendors' && $selectedRenovation)
            <!-- Vendors Tab -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Vendor Management</h3>
                    <p class="mt-1 text-sm text-gray-500">Manage vendors for: {{ $selectedRenovation->title }}</p>
                </div>
                
                <div class="p-4">
                    <livewire:property-renovations.vendor-management :renovationId="$selectedRenovationId" />
                </div>
            </div>
        @elseif($activeTab === 'expenses' && $selectedRenovation)
            <!-- Expenses Tab -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Expense Management</h3>
                    <p class="mt-1 text-sm text-gray-500">Track expenses for: {{ $selectedRenovation->title }}</p>
                </div>
                
                <div class="p-4">
                    <livewire:property-renovations.expense-management :renovationId="$selectedRenovationId" />
                </div>
            </div>
        @endif
    </div>
</div>
