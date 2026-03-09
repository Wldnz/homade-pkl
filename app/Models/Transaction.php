<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'subtotal',
        'shipping_cost',
        'sub_total',
        'total_price',
        'category',
        'status',
        'status_delivery',
        'refund_status',
        'refund_reason',
        'note',
        'delivery_at',
        'created_at',
        'updated_at'
    ];
 

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
