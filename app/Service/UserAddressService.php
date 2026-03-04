<?php
namespace App\Service;

use App\Models\User;
use App\Models\UserAddress;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Log;

class UserAddressService
{

    public function all()
    {
        // maximal memiliki 3 alamat saja
        return UserAddress::where('id_user', auth()->user()->id)
            ->orderBy('is_main_address', 'desc')
            ->get();
    }

    public function byID(string $id)
    {
        return UserAddress::where('id', $id)->first();
    }

    public function save(array $data)
    {
        return UserAddress::create($data);
    }

    public function saveAndChangeTheMainAdress(array $data): UserAddress
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['is_main_address']) && $data['is_main_address']) {
                UserAddress::where('id_user', $data['id_user'])
                    ->where('is_main_address', true)
                    ->update(['is_main_address' => false]);
            }
            return UserAddress::create($data);
        });
    }

    public function update(UserAddress $address, array $data)
    {
        return $address->update($data);
    }

    public function updateAndChangeTheMainAddress(UserAddress $address, array $data)
    {
        return DB::transaction(function () use ($address, $data) {
            if (isset($data['is_main_address']) && $data['is_main_address'] && !$address->is_main_address) {
                UserAddress::where('id_user', $address->id_user)
                    ->where('is_main_address', true)
                    ->update(['is_main_address' => false]);
                }
            $address->update($data);
            return $address;
        });
    }

    public function remove(UserAddress $address)
    {
        // kalo yang dihapus adalah main_address dan masih ada user address lainnya maka salah satu dari itu akan menjadi main address
        return DB::transaction(function () use ($address) {
            if($address->is_main_address){
                UserAddress::where('id_user', $address->id_user)
                ->first()
                ->update(['is_main_address' => true]);
            }
            return $address->delete();
        });
    }


}
