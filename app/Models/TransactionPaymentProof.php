<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TransactionPaymentProof extends Model
{
    use HasUuids;
    
     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_transaction',
        'public_id',
        'url',
        'reason',
        'created_at',
        'updated_at'
    ];
}
