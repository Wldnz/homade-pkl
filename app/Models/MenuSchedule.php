<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MenuSchedule extends Model
{
    use HasUuids;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_menu',
        'date_at',
        'created_at',
        'updated_at',
    ];

    public function menu(){
        return $this->belongsTo(Menu::class, 'id_menu')
        ->with([
            'menu_categories',
            'theme',
            // 'prices',
        ]);
    }

}
