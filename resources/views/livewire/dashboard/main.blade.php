<div class="container mx-auto py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ $user->name }}</h1>
        <p class="text-gray-600">{{ now()->format('l, F j, Y') }}</p>
    </div>
    
    <!-- Admin Dashboard -->
    @if($role === 'admin')
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Properties</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['total_properties'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Units</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['total_units'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Landlords</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['total_landlords'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tenants</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['total_tenants'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Active Subscriptions</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['active_subscriptions'] }}</span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Recent Invoices</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($recentInvoices as $invoice)
                        <div class="px-6 py-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $invoice->unit->property->name }} - Unit {{ $invoice->unit->unit_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $invoice->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">KSh {{ number_format($invoice->amount, 2) }}</p>
                                    <p class="text-sm text-gray-500">{{ $invoice->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No invoices found
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Admin Actions</h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 gap-4">
                    <a href="#" class="block bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-medium text-blue-700">Manage Users</h4>
                        <p class="text-sm text-blue-600">Create and manage user accounts and roles</p>
                    </a>
                    
                    <a href="{{ route('subscriptions.plans') }}" class="block bg-purple-50 hover:bg-purple-100 p-4 rounded-lg border border-purple-200">
                        <h4 class="font-medium text-purple-700">Manage Subscription Plans</h4>
                        <p class="text-sm text-purple-600">Configure pricing plans and features</p>
                    </a>
                    
                    <a href="#" class="block bg-green-50 hover:bg-green-100 p-4 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-700">System Settings</h4>
                        <p class="text-sm text-green-600">Configure application settings and parameters</p>
                    </a>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Landlord Dashboard -->
    @if($role === 'landlord')
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Properties</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['properties'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Units</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['units'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Occupancy Rate</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['occupancy_rate'] }}%</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Collection Rate</span>
                <span class="text-3xl font-bold mt-1">{{ $stats['collection_rate'] }}%</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Repairs</span>
                <span class="text-3xl font-bold mt-1 {{ $stats['pending_maintenance'] > 0 ? 'text-orange-600' : '' }}">{{ $stats['pending_maintenance'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Overdue Bills</span>
                <span class="text-3xl font-bold mt-1 {{ $stats['overdue_invoices'] > 0 ? 'text-red-600' : '' }}">{{ $stats['overdue_invoices'] }}</span>
            </div>
        </div>
        
        @if($expiringSubscription || $recommendation)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        @if($expiringSubscription)
                            <p class="text-sm text-yellow-700">
                                Your {{ $activePlan->name }} subscription is expiring soon. <a href="{{ route('subscriptions.plans') }}" class="font-medium underline">Renew now</a>.
                            </p>
                        @elseif($recommendation)
                            <p class="text-sm text-yellow-700">
                                {{ $recommendation['reason'] }} Consider upgrading to the {{ $recommendation['plan']->name }} plan.
                                <a href="{{ route('subscriptions.plans') }}" class="font-medium underline">Learn more</a>.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Recent Invoices</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($recentInvoices as $invoice)
                        <div class="px-6 py-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $invoice->unit->property->name }} - Unit {{ $invoice->unit->unit_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $invoice->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">KSh {{ number_format($invoice->amount, 2) }}</p>
                                    <span class="inline-flex px-2 text-xs rounded-full {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No invoices found
                        </div>
                    @endforelse
                    
                    <div class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('invoices.index') }}" class="text-sm text-blue-600 hover:text-blue-900">View all invoices →</a>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Maintenance Requests</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($recentMaintenance as $request)
                        <div class="px-6 py-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $request->title }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $request->unit->property->name }} - Unit {{ $request->unit->unit_number }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 text-xs rounded-full 
                                        {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $request->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                    <p class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No maintenance requests found
                        </div>
                    @endforelse
                    
                    <div class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-blue-600 hover:text-blue-900">View all maintenance requests →</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Pending Vacate Notices</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($pendingNotices as $notice)
                        <div class="px-6 py-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $notice->unit->property->name }} - Unit {{ $notice->unit->unit_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">Tenant: {{ $notice->user->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">
                                        Moving Out: {{ Carbon\Carbon::parse($notice->move_out_date)->format('M d, Y') }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ Carbon\Carbon::parse($notice->move_out_date)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No pending vacate notices
                        </div>
                    @endforelse
                    
                    <div class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('vacate.index') }}" class="text-sm text-blue-600 hover:text-blue-900">View all vacate notices →</a>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Quick Actions</h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('properties.index') }}" class="block bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-medium text-blue-700">Manage Properties</h4>
                        <p class="text-sm text-blue-600">Add or edit your properties</p>
                    </a>
                    
                    <a href="{{ route('invoices.index') }}" class="block bg-green-50 hover:bg-green-100 p-4 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-700">Create Invoice</h4>
                        <p class="text-sm text-green-600">Bill your tenants for rent and utilities</p>
                    </a>
                    
                    <a href="{{ route('maintenance.index') }}" class="block bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-medium text-yellow-700">Maintenance</h4>
                        <p class="text-sm text-yellow-600">View and manage repair requests</p>
                    </a>
                    
                    <a href="{{ route('reviews.index') }}" class="block bg-purple-50 hover:bg-purple-100 p-4 rounded-lg border border-purple-200">
                        <h4 class="font-medium text-purple-700">Reviews</h4>
                        <p class="text-sm text-purple-600">See what tenants are saying</p>
                    </a>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Tenant Dashboard -->
    @if($role === 'tenant')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Due</span>
                <span class="text-3xl font-bold mt-1 {{ $stats['total_due'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                    KSh {{ number_format($stats['total_due'], 2) }}
                </span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Overdue Bills</span>
                <span class="text-3xl font-bold mt-1 {{ $stats['overdue'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['overdue'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Maintenance Requests</span>
                <span class="text-3xl font-bold mt-1 text-gray-900">{{ $stats['maintenance_requests'] }}</span>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Requests</span>
                <span class="text-3xl font-bold mt-1 {{ $stats['pending_maintenance'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">{{ $stats['pending_maintenance'] }}</span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Pending Bills</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($recentInvoices->where('status', 'pending') as $invoice)
                        <div class="px-6 py-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $invoice->unit->property->name }} - Unit {{ $invoice->unit->unit_number }}
                                    </p>
                                    <p class="text-sm text-gray-500">Due: {{ Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">KSh {{ number_format($invoice->amount, 2) }}</p>
                                    <a href="{{ route('invoices.index') }}" class="text-sm text-blue-600 hover:text-blue-900">Pay Now</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No pending bills found
                        </div>
                    @endforelse
                    
                    <div class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('invoices.index') }}" class="text-sm text-blue-600 hover:text-blue-900">View all bills →</a>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Recent Maintenance Requests</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($recentMaintenance as $request)
                        <div class="px-6 py-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $request->title }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $request->unit->property->name }} - Unit {{ $request->unit->unit_number }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 text-xs rounded-full 
                                        {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $request->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $request->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                    <p class="text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-4 text-center text-gray-500">
                            No maintenance requests found
                        </div>
                    @endforelse
                    
                    <div class="px-6 py-3 bg-gray-50">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-blue-600 hover:text-blue-900">View all maintenance requests →</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Quick Actions</h3>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('maintenance.index') }}" class="block bg-blue-50 hover:bg-blue-100 p-4 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-700">Report Maintenance Issue</h4>
                    <p class="text-sm text-blue-600">Request repairs or maintenance</p>
                </a>
                
                <a href="{{ route('invoices.index') }}" class="block bg-green-50 hover:bg-green-100 p-4 rounded-lg border border-green-200">
                    <h4 class="font-medium text-green-700">Pay Bills</h4>
                    <p class="text-sm text-green-600">View and pay your pending invoices</p>
                </a>
                
                <a href="{{ route('vacate.index') }}" class="block bg-yellow-50 hover:bg-yellow-100 p-4 rounded-lg border border-yellow-200">
                    <h4 class="font-medium text-yellow-700">Give Vacate Notice</h4>
                    <p class="text-sm text-yellow-600">Submit notice when moving out</p>
                </a>
                
                <a href="{{ route('reviews.index') }}" class="block bg-purple-50 hover:bg-purple-100 p-4 rounded-lg border border-purple-200">
                    <h4 class="font-medium text-purple-700">Leave a Review</h4>
                    <p class="text-sm text-purple-600">Share your rental experience</p>
                </a>
            </div>
        </div>
    @endif
</div>
