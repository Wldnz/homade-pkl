<?php

namespace App\Utils;

use App\Service\ContactService;

class CalculateDistance
{

    private ContactService $contactService;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->contactService = new ContactService();
    }
    public function calculate($latTo, $lonTo)
{
    $earthRadius = 6371; // Jari-jari bumi dalam Kilometer

    $address = $this->contactService->address();

    if(!$address || !$address->longitude || !$address->latitude){
        return [
            'is_success' => false,
        ];
    }

    $latDelta = deg2rad($latTo - $address->latitude);
    $lonDelta = deg2rad($lonTo - $address->longitude);

    $a = sin($latDelta / 2) * sin($latDelta / 2) +
    cos(deg2rad($address->latitude)) * cos(deg2rad($latTo)) *
    sin($lonDelta / 2) * sin($lonDelta / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

     return [
            'distance' => $earthRadius * $c,
            'is_success' => true,
        ];
}
}
