<?php

namespace App\Service;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(
        
    ){
        return PaymentMethod::all();
    }

}
