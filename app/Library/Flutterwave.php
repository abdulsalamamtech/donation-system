<?php

namespace App\Library;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class Flutterwave
{
    // Public key
    private string $public_token;

    // Secrete key
    private string $secret_token;

    // Encryption key
    private string $encryption_key;

    // Payment link
    private string $payment_link;

    // Verify transaction link
    private string $verify_transaction_link;

    public function __construct(){
        #Public key
        $this->public_token = env('FLUTTERWAVE_PUBLIC_TOKEN');
        #Secrete key
        $this->secret_token = env('FLUTTERWAVE_SECRET_TOKEN');
        #Encryption key
        $this->encryption_key = env('FLUTTERWAVE_ENCRYPTION_KEY');
        #Payment link
        $this->payment_link = env('FLUTTERWAVE_PAYMENT_LINK');
        #Verify transaction link
        $this->verify_transaction_link = env('FLUTTERWAVE_VERIFY_TRANSACTION_LINK');
    }

    // Set header
    private function headers(){
        return $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '. $this->public_token
        ];
    }

    private function uniqueRef(){
        $random_var = md5(md5(random_bytes(32)) . md5(time()));
        return time() ."-" . $random_var;
    }

    public function makePayment(array $data = [])
    {
        $unique_ref = "flt-tx-" . $this->uniqueRef();
        $user = $data['user'];

        $body = [
            'tx_ref' => $data['tx_ref']?? Str::slug($unique_ref),
            'amount' => $data['amount'],
            'currency' => $data['currency']?? "NGN",
            'redirect_url' => $data['callback']?? route('flutterwave-callback'),
            'meta' => [
                'consumer_id' => $user['id'],
                'consumer_mac' => "92a3-912ba-1192a"
            ],
            'customer' => [
                'email' => $user['email'],
                'phonenumber' => $user['phone_number'],
                'name' => $user['name']
            ],
            'customizations'=> [
                'title' => "Make payment for donation",
                'logo' => "https://scontent-los2-1.xx.fbcdn.net/v/t39.30808-6/420214158_1895658077538840_7828014543620266523_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=5f2048&_nc_ohc=E9KZsgw5stYAb7J7WBD&_nc_ht=scontent-los2-1.xx&oh=00_AfAfhLO4xeRd9B1A9XaRM7qzdPHU-D6azgk3x1j6g1E_sg&oe=6618A7E1"
            ]
        ];

        $response = Http::withHeaders($this->headers())
            ->withToken($this->secret_token)
            ->retry(3, 100)
            ->post($this->payment_link, $body);

        if($response->successful()){
            $result = $response->json();
            $result['tx_ref'] = $body['tx_ref'];
            return $result;
        }
        return $this->errorResponse();

    }

    public function verifyPayment(array $data = []){

        $payment_id = $data['transaction_id'];
        $payment_ref = $data['tx_ref'];

        // Verify with id
        if($payment_id){
            // https://api.flutterwave.com/v3/transactions/payment_id/verify.
            $link = $this->verify_transaction_link . "/" . $payment_id . "/verify";

            $response = Http::withHeaders($this->headers())
                ->withToken($this->secret_token)
                ->get($link);

        // Verify with tx_ref if verification with id fails
        }elseif($payment_ref){
            // "https://api.flutterwave.com/v3/transactions/verify_by_reference?ref=flt-re-234543-3654";
            $link = $this->verify_transaction_link . "/verify_by_reference" . "?tx_ref=" . $payment_ref;

            $response = Http::withHeaders($this->headers())
                ->withToken($this->secret_token)
                ->get($link);
        }

        if($response->successful()){
            $payment_data = $response['data'];
            $data = [
                'status' => true,
                'transaction_id' => $payment_data['id'],
                'tx_ref' => $payment_data['tx_ref'],
                'amount' => $payment_data['amount'],
                'currency' => $payment_data['currency'],
                'customer' => $payment_data['customer'],
                'code' => 200,
            ];
            return $data;
        }

        return $this->errorResponse();

    }

    public function verifyPaymentWebHook(Request $request){
        if($request['event'] == "payment.completed"){
            $payment_data = $request->only(['data']);
            $data = [
                'transaction_id' => $payment_data['id'],
                'tx_ref' => $payment_data['tx_ref'],
            ];

            // Verify payment
            $response = $this->verifyPayment($data);
            http_response_code(200);
            return $response['code'];

        }

        return $this->errorResponse();

    }

    private function errorResponse(){
        return response()->json([
                'status' => false,
                'message' => 'Service Unavailable'
            ], 503);
    }

}
