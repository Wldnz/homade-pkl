<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuids;

    public function orders(){
        return $this->hasMany(Order::class, 'id_transaction')
        ->with('menu_price');        
    }

    public function address(){
        return $this->hasOne(TransactionAddress::class, 'id_transaction');
    }

    public function payment_proof() {
        return $this->hasOne(TransactionPaymentProof::class, 'id_transaction');
    }
}
