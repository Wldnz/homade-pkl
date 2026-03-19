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

    public function byID(string $id, $is_from_user = true)
    {
        return UserAddress::where('id', $id)
        ->when($is_from_user, function($query){
            return $query->where('id_user', auth()->user()->id);
        })
        ->first();
    }

    public function save(array $data)
    {
        return UserAddress::create($data);
    }

    public function saveAndChangeTheMainAdress(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                if (isset($data['is_main_address']) && $data['is_main_address']) {
                    $address = UserAddress::where('id_user', $data['id_user'])
                        ->where('is_main_address', true)
                        ->first();
                    if ($address)
                        $address->update(['is_main_address' => false]);
                }
                return UserAddress::create($data);
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error When save and change the address : '. $e->getMessage());
            return null;
        }
    }

    public function update(UserAddress $address, array $data)
    {
        return $address->update($data);
    }

    public function updateAndChangeTheMainAddress(UserAddress $address, array $data)
    {
        try {
            return DB::transaction(function () use ($address, $data) {
                // alamat adalah main dan dan ingin merubah ke ke tidak menjadi main alamat
                if ($address->is_main_address && isset($data['is_main_address']) && !$data['is_main_address']) {
                    // cek apakah alamat yang sudah menjadi main?
                    $m_address = UserAddress::where([
                        'id_user' => $address->id_user,
                        'is_main_address' => true,
                    ])->first();
                    if (!$m_address) {
                        // ubah alamat lain ini menjadi main
                        $f_address = UserAddress::where([
                            'id_user' => $address->id_user,
                            'is_main_address' => false,
                        ])->first();
                        if ($f_address)
                            $f_address->update(['is_main_address' => true]);
                    }
                } else if (!$address->is_main_address && isset($data['is_main_address']) && $data['is_main_address']) {
                    $f_address = UserAddress::where('id_user', $address->id_user)
                        ->where('is_main_address', true)
                        ->first();
                    if ($f_address)
                        $f_address->update(['is_main_address' => false]);
                }
                $address->update($data);
                return $address;
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error When updating the address : ' . $e->getMessage());
            return null;
        }
    }

    public function remove(UserAddress $address)
    {
        // kalo yang dihapus adalah main_address dan masih ada user address lainnya maka salah satu dari itu akan menjadi main address
        try {
            return DB::transaction(function () use ($address) {
                if ($address->is_main_address) {
                    UserAddress::where('id_user', $address->id_user)
                        ->first()
                        ->update(['is_main_address' => true]);
                }
                return $address->delete();
            });
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error When delete the address : ' . $e->getMessage());
            return null;
        }
    }


}
