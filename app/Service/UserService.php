<?php
namespace App\Service;

use App\Models\User;
use Auth;
use Hash;

class UserService
{
    // dokumentasi ini gimana ya? kayak ada param parammnya gitu
    // ada log, alert?, response
    public function login(
        array $credential,
        bool $isRemember = false
    ){ 
        if(Auth::attempt($credential, $isRemember)){
            session()->regenerate();

        }
    }

    public function register($user){
        $user['password'] = Hash::make($user['password']);
        User::save($user);

        // if()

    } 

}
