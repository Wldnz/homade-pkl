<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TransactionOrder extends Model
{
    use HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_transaction',
        'id_menu_price',
        'id_menu',
        'total_price',
        'price_at_purchase',
        'quantity',
        'note',
        'delivery_at',
        'created_at',
        'updated_at'
    ];
}
