<?php

namespace App\Http\Controllers;

use App\Models\HotspotPlan;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Iankumu\Mpesa\Facades\Mpesa;

class MpesaController extends Controller
{
    //
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'plan_id' => 'required|exists:hotspot_plans,id'
        ]);

        $plan = HotspotPlan::find($validated['plan_id']);

        $response = Mpesa::stkpush([
            'amount' =>$validated['amount'],
            'phone' => $validated['phone'],
            'reference' => 'HOTSPOT-' . strtoupper(Str::random(6)),
            'description' => 'payment for ' . $plan->name,
            'callback' => env('MPESA_CALLBACK_URL'),
        ]);

        if($response->ResponseCode == '0')
        {
            Payment::create([
                'phone' => $validated['phone'],
                'amount' => $validated['amount'],
                'transaction_id' =>$response->CheckoutRequestID,
                'status' => 'pending',
                'plan_id' => $plan->id
            ]);

            return response()->json(['message' => 'STK PUSH sent. Please approve']);
        }
        return response()->json(['error' => 'Failed to initiate STK Push'], 500);
    }

    public function handleCallback(Request $request)
    {
        $data = $request->all();

        if (isset($data['Body']['stkCallback']['ResultCode']) && $data['Body']['stkCallbback']['ResultCode'] == 0) {
            $metadata = $data['Body']['stkCallback']['CallbackMetadata']['Item'];
            $amount = collect($metadata)->where('Name', 'Amount')->first()['Value'];
            $phone = collect($metadata)->where('Name', 'PhoneNumber')->first()['Value'];
            $transId = $data['Body']['stkCallback']['CheckoutRequestID'];

            //Find payment
            $payment = Payment::where('transaction_id', $transId)->first();

            if($payment) {
                $payment->update(['status' => 'completed']);

                return response()->json(['message' => 'Payment successful']);
            }
        }

        return response()->json(['message' => 'Payment failed or cancelled'], 400);
    }
    
}
