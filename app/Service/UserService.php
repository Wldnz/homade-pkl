<?php
namespace App\Service;

use App\Models\User;
use Auth;
use Exception;
use Log;

class UserService
{

    public function currentUser(
    ) {
        return User::where('id', auth()->user()->id)
            ->with([
                'address',
                'orders' => fn($orders) => $orders->limit(3)
            ])
            ->first();
    }

    public function getByEmail(
        string $email,
    ) {
        return User::where('email', $email)->first();
    }

    public function save(array $data)
    {
        return User::create($data);
        // try {
        // } catch (Exception $e) {
        //     Log::error($e->getMessage());
        //     throw new Exception('Gagal Menyimpan user ke dalam database!');
        // }
    }

    public function login(User $user, $isRemember = true)
    {
        return Auth::login($user, $isRemember);
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerate();
    }


}
