<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription Plans') }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <h1 class="text-2xl font-bold mb-4">Choose a Subscription Plan</h1>
        @livewire('subscriptions.plans')
    </div>
</x-app-layout>