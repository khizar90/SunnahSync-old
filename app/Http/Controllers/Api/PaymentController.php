<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
class PaymentController extends Controller
{
    public function craeteIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required',
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            $stripeId = null;
            if ($user->customer_id)
                $stripeId = $user->customer_id;
            // Stripe::setApiKey(config(key: 'app.stripe_secret'));
            Stripe::setApiKey('sk_test_51Nw7pWKpFYh52QT96LT9j16mVHhJOfOkk3GmXtTtBhY9KgHSA9DwRfRFLkojiGYX3uwK8hDtaTIq3gaIUFn9Ecik00OCNRtNuB');
            
            if ($user->customer_id === '') {

                $person = Customer::create([
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'name' => $user->name,
                    'description' => ''
                ]);
                if (!$stripeId) {

                    $stripeId = $person['id'];
                    User::where('id', $user->id)->update(['customer_id' => $stripeId]);
                }
            }
            $user = User::find($request->user_id);
            $intent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'customer' => $user->customer_id
            ]);

            return response()->json([
                'status' => true,
                'action' =>  'Intent Created',
                'data' => $intent->client_secret
            ]);
        }
    }
}
