<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TransactionAddress extends Model
{
    use HasUuids;
    protected $table = 'transaction_address';
}
