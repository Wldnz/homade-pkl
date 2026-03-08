<?php
namespace App\Service;

use App\Models\User;
use App\Models\UserAddress;
use App\UserRole;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Log;

class UserService
{

    public function currentUser(
    ) {
        return auth()->user();
    }

    public function getByEmail(
        string $email,
        ?bool $isManagement = false, 
    ) {
        $user = User::where('email', $email)->first();
        if ($isManagement && $user && !in_array($user->role, [ UserRole::ADMIN, UserRole::CUSTOMER ])) return null;

        return $user;
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
