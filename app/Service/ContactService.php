<?php

namespace App\Service;

use App\Models\Setting;

class ContactService
{

    public function information() {
        return Setting::first();
    }

}