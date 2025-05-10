<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\DarajaService;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function plans()
    {
        $plans = SubscriptionPlan::all();
        return view('subscriptions.plans', compact('plans'));
    }

    public function checkout(Request $request, DarajaService $daraja)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'phone' => 'required',
        ]);

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);

        $response = $daraja->stkPush($data['phone'], $plan->price, 'Subscription', $plan->name);

        // Record pending payment
        $payment = auth()->user()->payments()->create([
            'amount' => $plan->price,
            'method' => 'mpesa',
            'status' => 'pending',
            'transaction_id' => $response['CheckoutRequestID'] ?? null,
        ]);

        session()->flash('status', 'M-Pesa payment initiated. Check your phone to complete.');
        return redirect()->back();
    }

    public function callback(Request $request)
    {
        $payload = $request->input('Body');
        $callback = $payload['stkCallback'] ?? null;

        if (! $callback) {
            return response()->json(['error' => 'Invalid callback'], 400);
        }

        $checkoutId = $callback['CheckoutRequestID'];
        $resultCode = $callback['ResultCode'];

        $payment = Payment::where('transaction_id', $checkoutId)->first();
        if (! $payment) {
            return response()->json(['error' => 'Payment record not found'], 404);
        }

        if ($resultCode === 0) {
            $payment->update(['status' => 'completed', 'paid_at' => now()]);

            // Activate subscription
            $plan = $payment->subscription->subscriptionPlan ?? null;
            $duration = $plan->duration_months ?? 1;
            $subscription = auth()->user()->subscriptions()->create([
                'subscription_plan_id' => $plan->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths($duration),
                'status' => 'active',
            ]);
        } else {
            $payment->update(['status' => 'failed']);
        }

        return response()->json(['success' => true]);
    }
}
