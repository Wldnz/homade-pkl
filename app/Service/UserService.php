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

    public function all(
        string|null $search = null,
        $limit = 15,
        bool $is_has_limit = true,
        bool $with_address = false,
        string|null $role = null
    ){
        $users = User::when($role, function($query, $role){
            return $query->where('role', $role);
        })
        ->when($with_address, function($query){
            return $query->with('address');
        })
        ->when($search, function($query, $search){
            $search = strtolower($search);
            return $query->whereRaw('LOWER(name) LIKE' , ["%$search%"]);
        });
        if($is_has_limit){
            return $users->paginate($limit);
        }
        return $users->get();
    }

    public function currentUser(
    ) {
        return auth()->user();
    }

    public function getByID(string $id){
        return User::where('id', $id)->first();
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
