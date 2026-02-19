<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    public function menu_price(){
        return $this->belongsTo(MenuPrice::class, 'id_menu_price')
        ->with([
            'menu',
            'package'
        ]);
    }

}
