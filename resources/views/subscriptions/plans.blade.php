@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Choose a Subscription Plan</h1>
    @livewire('subscriptions.plans')
</div>
@endsection