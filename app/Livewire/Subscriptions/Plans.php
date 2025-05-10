<?php

namespace App\Livewire\Subscriptions;

use Livewire\Component;
use App\Models\SubscriptionPlan;
use App\Services\DarajaService;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class Plans extends Component
{
    public $plans;
    public $plan_id;
    public $phone;

    public function mount()
    {
        $this->plans = SubscriptionPlan::all();
    }

    public function checkout(DarajaService $daraja)
    {
        $data = $this->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'phone' => 'required',
        ]);

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        $response = $daraja->stkPush($data['phone'], $plan->price, 'Subscription', $plan->name);

        Auth::user()->payments()->create([
            'amount' => $plan->price,
            'method' => 'mpesa',
            'status' => 'pending',
            'transaction_id' => $response['CheckoutRequestID'] ?? null,
        ]);

        session()->flash('status', 'M-Pesa payment initiated. Check your phone.');
        return redirect()->to(route('subscriptions.plans'));
    }

    public function render()
    {
        return view('livewire.subscriptions.plans');
    }
}
