<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'currency',
        'payment_provider',
        'payment_type',
        'transaction_ref',
        'transaction_id',
        'payment_initiated',
        'payment_completed',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
