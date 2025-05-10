@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-6">M-Pesa Payments</h1>
            
            <div class="mb-6">
                <p class="text-gray-600">
                    Make payments using M-Pesa for your rent, utilities, and other services. Simply enter your phone number, 
                    the amount you want to pay, and follow the instructions.
                </p>
            </div>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
                <p class="font-medium">Simulation Mode</p>
                <p>This is a simulated payment environment for testing purposes. No actual M-Pesa transactions will be processed.</p>
            </div>
            
            <div class="mt-8">
                @livewire('payments.mpesa-payment')
            </div>
            
            <div class="mt-10 pt-6 border-t border-gray-200">
                <h2 class="text-lg font-semibold mb-4">About M-Pesa Payments</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium mb-2">How it works</h3>
                        <ol class="list-decimal list-inside text-gray-700 space-y-1">
                            <li>Enter your phone number and payment amount</li>
                            <li>Confirm payment details</li>
                            <li>You'll receive an STK push notification on your phone</li>
                            <li>Enter your M-Pesa PIN to complete the transaction</li>
                            <li>Receive confirmation and receipt of your payment</li>
                        </ol>
                    </div>
                    <div>
                        <h3 class="font-medium mb-2">Benefits</h3>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Fast and secure payments</li>
                            <li>No need to visit our offices</li>
                            <li>Pay from anywhere, anytime</li>
                            <li>Instant confirmation</li>
                            <li>Digital receipts for all transactions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection