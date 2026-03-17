<?php

namespace App\Utils;

use Carbon\Carbon;
use Date;
use Exception;

class ConvertDateSafely
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function convert(
        string $date_at,
        string|Carbon|Date $default_date = null,
    ){
        try{
            return Carbon::parse($date_at);
        }catch(Exception $e){
            return $default_date;
        }
    }

}
