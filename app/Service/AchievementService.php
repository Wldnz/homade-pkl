<?php

namespace App\Service;

use App\Models\Achievement;

class AchievementService{

    public function all(array $columns=[ 'name', 'description', 'date_at'  ]){
        return Achievement::all($columns);
    }

}