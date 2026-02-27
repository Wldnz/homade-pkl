<?php

namespace App\Service;

use App\Models\Partner;

class PartnerService{

    public function all(
        array $columns=[ 'name', 'name', 'image_url' ],
        int|null $limit = 30,
    ){
        return Partner::paginate($limit, $columns);
    }

}