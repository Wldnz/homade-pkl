<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuPrice extends Model
{
    use HasUuids;

    public function packages(){
        return $this->belongsTo(Package::class, 'id_package')
        ->select(['id', 'name', 'description', 'minimum_order', 'image_url']);
    }

}
