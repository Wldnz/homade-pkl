<?php

namespace App\Service;

use App\Models\Partner;

class PartnerService{

    public function all(
        array $columns=[ 'name', 'name', 'image_url' ],
        int|null $limit = null,
    ){
        return Partner::when($limit, function($query, $limit){
            return $query->limit($limit);
        })
        ->select($columns)->get();
    }

}