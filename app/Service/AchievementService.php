<?php

namespace App\Service;

use App\Models\Achievement;

class AchievementService{

    public function all(array $columns=['*']){
        return Achievement::all($columns);
    }

}