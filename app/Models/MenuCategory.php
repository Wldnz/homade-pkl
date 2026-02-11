<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasUuids;

    public function categories(){
        return $this->belongsTo(Category::class, 'id_category')
        ->select(['id' , 'name']);
    }

}
