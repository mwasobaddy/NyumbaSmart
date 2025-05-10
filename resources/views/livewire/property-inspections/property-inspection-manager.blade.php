<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Property Inspections
                </h2>
                <div>
                    <button type="button" wire:click="newInspection" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Schedule New Inspection
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-4 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="#" 
                        wire:click.prevent="$set('activeTab', 'list')" 
                        class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'list' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        All Inspections
                    </a>
                    <a href="#" 
                        wire:click.prevent="$set('activeTab', 'schedule')" 
                        class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'schedule' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Schedule Inspection
                    </a>
                    @if($selectedInspectionId)
                        <a href="#" 
                            wire:click.prevent="$set('activeTab', 'report')" 
                            class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'report' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Inspection Report
                        </a>
                        <a href="#" 
                            wire:click.prevent="$set('activeTab', 'photos')" 
                            class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'photos' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Photo Documentation
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Tab Content -->
            <div>
                @if($activeTab === 'list')
                    <!-- Inspection List -->
                    <div>
                        <!-- Search and filters -->
                        <div class="bg-white p-4 rounded-lg shadow mb-4">
                            <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-6 sm:gap-x-4">
                                <!-- Search -->
                                <div class="sm:col-span-2">
                                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                    <div class="mt-1">
                                        <input type="text" wire:model.debounce.300ms="search" id="search" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Search by unit or tenant">
                                    </div>
                                </div>
                                
                                <!-- Status Filter -->
                                <div class="sm:col-span-1">
                                    <label for="filter-status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <div class="mt-1">
                                        <select wire:model="filterStatus" id="filter-status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                            <option value="">All Statuses</option>
                                            <option value="scheduled">Scheduled</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Type Filter -->
                                <div class="sm:col-span-1">
                                    <label for="filter-type" class="block text-sm font-medium text-gray-700">Type</label>
                                    <div class="mt-1">
                                        <select wire:model="filterType" id="filter-type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                            <option value="">All Types</option>
                                            <option value="move_in">Move-in</option>
                                            <option value="move_out">Move-out</option>
                                            <option value="routine">Routine</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Date Range Filter -->
                                <div class="sm:col-span-2">
                                    <label for="date-range" class="block text-sm font-medium text-gray-700">Date Range</label>
                                    <div class="mt-1">
                                        <select wire:model="dateRange" id="date-range" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                            <option value="">All Dates</option>
                                            <option value="upcoming">Upcoming</option>
                                            <option value="past">Past</option>
                                            <option value="this_week">This Week</option>
                                            <option value="this_month">This Month</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inspection Table -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                @if($inspections->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signed</th>
                                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($inspections as $inspection)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $inspection->unit->name ?? 'Unit ' . $inspection->unit_id }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $inspection->tenant->name ?? 'Unknown' }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ ucfirst(str_replace('_', ' ', $inspection->type)) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $inspection->inspection_date->format('M d, Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                                                                $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                                ($inspection->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') 
                                                            }}">
                                                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            @if($inspection->tenant_signed && $inspection->landlord_signed)
                                                                <span class="text-green-600">Both</span>
                                                            @elseif($inspection->tenant_signed)
                                                                <span>Tenant Only</span>
                                                            @elseif($inspection->landlord_signed)
                                                                <span>Landlord Only</span>
                                                            @else
                                                                <span class="text-red-600">Not Signed</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <button type="button" wire:click="showReport({{ $inspection->id }})" class="text-primary-600 hover:text-primary-900">Report</button>
                                                            <button type="button" wire:click="showPhotos({{ $inspection->id }})" class="ml-3 text-primary-600 hover:text-primary-900">Photos</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Pagination -->
                                    <div class="mt-4">
                                        {{ $inspections->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-10">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No inspections found</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Get started by scheduling a new property inspection.
                                        </p>
                                        <div class="mt-6">
                                            <button type="button" wire:click="newInspection" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Schedule New Inspection
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($activeTab === 'schedule')
                    <!-- Schedule Inspection Form -->
                    <livewire:property-inspections.schedule-inspection />
                @elseif($activeTab === 'report' && $selectedInspectionId)
                    <!-- Inspection Report -->
                    <livewire:property-inspections.inspection-report :inspectionId="$selectedInspectionId" :key="'report-'.$selectedInspectionId" />
                @elseif($activeTab === 'photos' && $selectedInspectionId)
                    <!-- Photo Documentation -->
                    <livewire:property-inspections.photo-documentation :inspectionId="$selectedInspectionId" :key="'photos-'.$selectedInspectionId" />
                @endif
            </div>
        </div>
    </div>
</div>
