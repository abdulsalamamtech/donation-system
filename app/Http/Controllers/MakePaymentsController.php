<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Library\Flutterwave;
use App\Models\Payment;
use App\Models\User;


class MakePaymentsController extends Controller
{

    public function Pay()
    {
        $provider = 'flutterwave';
        $amount = 25000;
        $user = auth()->user();
        $data = array(
            'user' => $user,
            'amount' => $amount,
        );

        $flutterwave = new Flutterwave();
        $make_payment = $flutterwave->makePayment($data);

        if($make_payment['status']){

            $payment = Payment::create([
                'user_id' => $user['id'],
                'transaction_ref' => $make_payment['tx_ref'],
                'amount' => $amount,
                'payment_provider' => $provider,
                'payment_initiated' => now(),
            ]);

            return redirect()->away($make_payment['data']['link']);
        }


    }
    public function verifyPayment(Request $request)
    {
        $data = $request->all();
        $flutterwave = new Flutterwave();
        $verify_payment = $flutterwave->verifyPayment($data);

        if($verify_payment['status']){
            // $tx_ref = "flt-tx-1712502726-ef73fe8ffdd4aed19e894aa2d500e71e";
            // $payment = Payment::where('transaction_ref', $tx_ref)->first();

            $payment = Payment::where('transaction_ref', $verify_payment['tx_ref'])->firstOrFail();   

            $r['0'] = $payment;
            $payment->update([
                'status' => 'successful',
                'transaction_id' => $data['transaction_id'],
                'payment_completed' => now(),
            ]);

            return view('payments.success');

        }
        
    }

    public function successfulPaymentWebHook(){
        
    }

    public function payments(){
        $payments = Payment::with('user')->paginate(10);

        return view('payments.index', ['payments' => $payments]);
    }

}
