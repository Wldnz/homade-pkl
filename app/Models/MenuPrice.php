<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuPrice extends Model
{
    use HasUuids;

    public function menu(){
        return $this->belongsTo(Menu::class, 'id_menu')
        ->with([
            'theme',
            'menu_categories'
        ]);
    }

    public function package(){
        return $this->belongsTo(Package::class, 'id_package')
        ->select(['id', 'name', 'description', 'minimum_order', 'image_url']);
    }

}
