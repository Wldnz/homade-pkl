<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasUuids;

    public function menu_categories(){
        return $this->hasMany(MenuCategory::class, foreignKey: 'id_menu')
        ->with("categories")
        ->select(['id', 'id_menu', 'id_category']);
    }

    public function theme(){
        return $this->belongsTo(Theme::class, 'id_theme')
        ->select(['id', 'name']);
    }

    public function prices(){
        return $this->hasMany(MenuPrice::class, 'id_menu')
        ->with('packages')
        ->select([ 'id', 'id_menu', 'id_package', 'price' ]);
    }


}
