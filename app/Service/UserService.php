<?php
namespace App\Service;

use App\Models\User;
use App\Models\UserAddress;
use Auth;
use Exception;
use Log;

class UserService
{

    public function currentUser(
    ) {
        return auth()->user();
    }

    public function getByEmail(
        string $email,
    ) {
        return User::where('email', $email)->first();
    }

    public function save(array $data)
    {
        return User::create($data);
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
