<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasUuids;

    protected $table = 'user_address';

    protected $fillable = [
        'id_user',
        'received_name',
        'phone',
        'label',
        'address',
        'note',
        'longitude',
        'latitude'
    ];
}
