<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_category',
        'id_menu',
        'created_at',
        'updated_at',
    ];

    public function categories(){
        return $this->belongsTo(Category::class, 'id_category')
        ->select(['id' , 'name']);
    }

}
