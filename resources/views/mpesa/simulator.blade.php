@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">M-Pesa Payment Simulator</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Initiation Form -->
                <div class="border rounded-lg p-4 bg-gray-50">
                    <h3 class="text-lg font-semibold mb-3">Initiate Payment</h3>
                    
                    <form id="paymentForm" class="space-y-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="254712345678">
                        </div>
                        
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                            <input type="number" id="amount" name="amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="1" value="1">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                            <input type="text" id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="May Rent">
                        </div>
                        
                        <div>
                            <label for="invoice_id" class="block text-sm font-medium text-gray-700">Invoice ID (Optional)</label>
                            <input type="text" id="invoice_id" name="invoice_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="1">
                        </div>
                        
                        <div>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Pay with M-Pesa
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Payment Simulation Panel -->
                <div class="border rounded-lg p-4" id="simulationPanel" style="display: none;">
                    <h3 class="text-lg font-semibold mb-3">Payment Simulation</h3>
                    <div id="statusMessage" class="p-3 bg-yellow-100 text-yellow-800 rounded-md mb-4">
                        Waiting for payment initiation...
                    </div>
                    
                    <div id="transactionDetails" class="mb-4 space-y-2"></div>
                    
                    <div class="flex space-x-4">
                        <button id="btnSuccess" class="py-2 px-4 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Simulate Success
                        </button>
                        <button id="btnFail" class="py-2 px-4 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Simulate Fail
                        </button>
                    </div>
                    
                    <div id="receiptPanel" class="mt-6 p-4 border rounded-md bg-gray-50" style="display: none;">
                        <h4 class="font-semibold text-lg mb-2">Payment Receipt</h4>
                        <div id="receiptContent" class="space-y-1"></div>
                        <div class="mt-4">
                            <button id="btnPrintReceipt" class="py-1 px-3 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">
                                Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let checkoutRequestId = '';
        
        // Handle payment form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                phone: document.getElementById('phone').value,
                amount: document.getElementById('amount').value,
                description: document.getElementById('description').value || undefined,
                invoice_id: document.getElementById('invoice_id').value || undefined,
            };
            
            // Display simulation panel
            document.getElementById('simulationPanel').style.display = 'block';
            document.getElementById('statusMessage').className = 'p-3 bg-yellow-100 text-yellow-800 rounded-md mb-4';
            document.getElementById('statusMessage').textContent = 'Initiating payment...';
            
            // Send the request to initiate payment
            fetch('/api/mpesa/stk-push', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkoutRequestId = data.data.CheckoutRequestID;
                    
                    // Update status message
                    document.getElementById('statusMessage').textContent = 'Payment request sent to your phone. Please enter your PIN or simulate the response below.';
                    
                    // Show transaction details
                    document.getElementById('transactionDetails').innerHTML = `
                        <div><strong>Transaction Amount:</strong> ${formData.amount}</div>
                        <div><strong>Phone Number:</strong> ${formData.phone}</div>
                        <div><strong>Description:</strong> ${formData.description || 'N/A'}</div>
                        <div><strong>Checkout Request ID:</strong> ${checkoutRequestId}</div>
                    `;
                } else {
                    document.getElementById('statusMessage').className = 'p-3 bg-red-100 text-red-800 rounded-md mb-4';
                    document.getElementById('statusMessage').textContent = 'Failed to initiate payment: ' + (data.message || 'Unknown error');
                }
            })
            .catch(error => {
                document.getElementById('statusMessage').className = 'p-3 bg-red-100 text-red-800 rounded-md mb-4';
                document.getElementById('statusMessage').textContent = 'Error: ' + error.message;
            });
        });
        
        // Handle successful payment simulation
        document.getElementById('btnSuccess').addEventListener('click', function() {
            if (!checkoutRequestId) {
                alert('Please initiate a payment first.');
                return;
            }
            
            simulateCallback(true);
        });
        
        // Handle failed payment simulation
        document.getElementById('btnFail').addEventListener('click', function() {
            if (!checkoutRequestId) {
                alert('Please initiate a payment first.');
                return;
            }
            
            simulateCallback(false);
        });
        
        // Function to simulate the M-Pesa callback
        function simulateCallback(success) {
            document.getElementById('statusMessage').className = 'p-3 bg-yellow-100 text-yellow-800 rounded-md mb-4';
            document.getElementById('statusMessage').textContent = `Simulating ${success ? 'successful' : 'failed'} payment...`;
            
            fetch('/api/mpesa/simulate-callback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    checkout_request_id: checkoutRequestId,
                    success: success
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (success) {
                        document.getElementById('statusMessage').className = 'p-3 bg-green-100 text-green-800 rounded-md mb-4';
                        document.getElementById('statusMessage').textContent = 'Payment completed successfully!';
                        
                        // Show receipt after successful payment
                        fetchReceipt();
                    } else {
                        document.getElementById('statusMessage').className = 'p-3 bg-red-100 text-red-800 rounded-md mb-4';
                        document.getElementById('statusMessage').textContent = 'Payment failed.';
                        document.getElementById('receiptPanel').style.display = 'none';
                    }
                } else {
                    document.getElementById('statusMessage').className = 'p-3 bg-red-100 text-red-800 rounded-md mb-4';
                    document.getElementById('statusMessage').textContent = 'Error: ' + data.message;
                    document.getElementById('receiptPanel').style.display = 'none';
                }
            })
            .catch(error => {
                document.getElementById('statusMessage').className = 'p-3 bg-red-100 text-red-800 rounded-md mb-4';
                document.getElementById('statusMessage').textContent = 'Error: ' + error.message;
                document.getElementById('receiptPanel').style.display = 'none';
            });
        }
        
        // Function to fetch payment receipt
        function fetchReceipt() {
            fetch(`/api/mpesa/receipt?checkout_request_id=${checkoutRequestId}`, {
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show receipt panel
                    document.getElementById('receiptPanel').style.display = 'block';
                    
                    // Format receipt content
                    document.getElementById('receiptContent').innerHTML = `
                        <div><strong>Receipt Number:</strong> ${data.data.receipt_number}</div>
                        <div><strong>Amount:</strong> KES ${data.data.amount}</div>
                        <div><strong>Phone Number:</strong> ${data.data.phone}</div>
                        <div><strong>Reference:</strong> ${data.data.reference}</div>
                        <div><strong>Description:</strong> ${data.data.description}</div>
                        <div><strong>Date:</strong> ${new Date(data.data.date).toLocaleString()}</div>
                    `;
                } else {
                    document.getElementById('receiptPanel').style.display = 'none';
                }
            })
            .catch(error => {
                document.getElementById('receiptPanel').style.display = 'none';
            });
        }
        
        // Handle print receipt
        document.getElementById('btnPrintReceipt').addEventListener('click', function() {
            window.print();
        });
    });
</script>
@endpush
@endsection