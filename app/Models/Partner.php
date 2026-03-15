<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasUuids;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'image_url',
        'image_public_id',
        'created_at',
        'updated_at'
    ];
}
