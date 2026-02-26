<?php
namespace App\Service;

use App\Models\User;
use App\Models\UserAddress;
use Auth;
use Exception;
use Log;

class UserAddressService
{

    public function all(){
        // maximal memiliki 3 alamat saja
        return UserAddress::where('id_user', auth()->user()->id)->get();
    }

    public function byID(string $id){
        return UserAddress::where('id', $id)->first();
    }

    public function save(array $data){
        return UserAddress::create($data);
    }

    public function update(UserAddress $address, array $data){
        return $address->update($data);
    }

    public function remove(){

    }


}
