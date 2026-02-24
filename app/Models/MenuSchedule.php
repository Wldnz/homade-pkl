<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuSchedule extends Model
{
    use HasUuids;

    public function menu(){
        return $this->belongsTo(Menu::class, 'id_menu')
        ->with([
            'menu_categories',
            'theme',
            // 'prices',
        ]);
    }

}
