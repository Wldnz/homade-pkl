<?php
namespace App\Service;

use App\Models\User;
use App\Models\UserAddress;
use App\UserRole;
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
        bool $isManagement,
    ) {
        return User::where('email', $email)
        ->when($isManagement, function($query, $isManagement){
            return $query->where('role', UserRole::OWNER)
            ->orWhere('role', UserRole::ADMIN);
        })
        ->first();
    }

    public function save(array $data)
    {
        $user = new User();
        $user->fill($data);
        $user->password = $data['password'];
        $user->save();
        return $user;
    }

    public function edit(User $user, array $data){
        return $user->update($data);
    }

    public function remove(User $user){
        return $user->delete();;
    }

    public function changePassword(User $user, string $hashedPassword){
        $user->password = $hashedPassword;
        $user->save();
        return $user;
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
