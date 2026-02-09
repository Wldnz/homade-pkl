<?php
namespace App\Service;

class UserService
{
    // dokumentasi ini gimana ya? kayak ada param parammnya gitu
    // ada log, alert?, response
    public function login(array $data){

        if(empty($data['email']) && empty($data['password'])){
            return json_encode([
                "message" => "Pastikan alamat email dan kata sandi itu sudah berisi",
                "code" => 400,
            ]);
        }
     
        return false;

    }
}
