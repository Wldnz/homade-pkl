<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TransactionAddress extends Model
{
    use HasUuids;
    protected $table = 'transaction_address';

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_transaction',
        'received_name',
        'phone',
        'label',
        'address',
        'note',
        'longitude',
        'latitude',
        'created_at',
        'updated_at'
    ];
}
