<?php

namespace App\Service;

use App\Models\Partner;

class PartnerService{

    public function all(array $columns=['*']){
        return Partner::all($columns);
    }

}