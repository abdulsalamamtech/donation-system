<?php

use App\Http\Controllers\MakePaymentsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\Http\Controllers\SendMailController;
use Illuminate\Support\Facades\Http;
use App\Models\Payment;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Auth Users Can Make Payment
Route::middleware('auth')->group(function () {

    // Call back
    Route::get('pay/callback', function (Request $request){
        $data['transaction_id'] = $request['transaction_id'];
        $data['tx_ref'] = $request['tx_ref'];
        return redirect()->route('verify-payment', $data);

    })->name('flutterwave-callback');

    // Make payment
    Route::get('pay', [MakePaymentsController::class, 'pay'])->name('make-payment');

    // Verify transaction
    Route::get('verify-transaction', [MakePaymentsController::class, 'verifyPayment'])->name('verify-payment');
    
    // Payment Successful
    Route::get('pay/successful', function (Request $request){
        return view('payments.success');
    })->name('payment-successful');
    
    Route::get('payments', [MakePaymentsController::class, 'payments'])->name('payments');

});















Route::get('/test/pay', function (){
    $payment = Payment::all();
    return $payment;
});
Route::get('/test', function (){
    $tx_ref = "flt-tx-1712502726-ef73fe8ffdd4aed19e894aa2d500e71e";
    $payment = Payment::where('transaction_ref', $tx_ref)->first();
    return $payment;
});













require __DIR__.'/auth.php';
