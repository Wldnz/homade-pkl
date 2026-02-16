<?php
namespace App\Service;

use App\Models\User;
use Auth;
use Exception;
use Hash;
use Log;

class UserService
{

    // public function getById(string $id){
    //     return User::find($id)->first();
    // }

    public function getByEmail(
        string $email,
    ){
        return User::where('email', $email)->first();
    }

    public function save(array $data){
        try{
            return User::create($data);
        }catch(Exception $e){
            Log::error($e->getMessage());
            throw new Exception('Gagal Menyimpan user ke dalam database!');
        }
    }

    public function login(User $user, $isRemember = true){
        return Auth::login($user, $isRemember);
    }


}
