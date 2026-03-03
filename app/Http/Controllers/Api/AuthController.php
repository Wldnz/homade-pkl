<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SuccessfullyRegistered;
use App\Models\User;
use App\ResponseData;
use App\Service\UserService;
use Auth;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Log;
use Mail;
use Validator;

class AuthController extends Controller
{

    private ResponseData $responseData;

    private UserService $userService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->userService = new UserService();
    }

    public function signin(Request $request)
    {
        try {
            $credential = Validator::make($request->all(), [
                'email' => 'required|email|min:8',
                'password' => 'required|min:8'
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus berupa email yang valid!',
                'min' => ':attribute minimal harus memiliki :min karater'
            ], [
                'email' => 'Alamat Email',
                'password' => 'Kata Sandi'
            ]);

            if ($credential->fails()) {
                return $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid Nih!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                );
            }

            $user = $this->userService->getByEmail($request->email);

            if (!$user) {
                return $this->responseData->create(
                    'Tidak dapat menemukan email',
                    status: 'warning',
                    status_code: 404,
                );
            }

            if (!Hash::check($request->password, $user->password)) {
                return $this->responseData->create(
                    'Password Yang Anda Masukkan Tidak Valid!',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil Masuk!',
                [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'token' => auth()->guard('api')->login($user),
                ],
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500
            );
        }
    }
    public function signup(Request $request)
    {
        try {
            $credential = Validator::make($request->all(), [
                'first_name' => 'required|min:2',
                'email' => 'required|min:8|email',
                'password' => [
                    'required',
                    Password::min(8)->mixedCase()->numbers()->symbols(),
                ],
                'password_confirmation' => 'required|same:password'
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus berupa email yang valid!',
                'min' => ':attribute minimal harus memiliki :min karater',
                'same' => ':attribute harus sama dengan :other',
                'password.mixed' => ':attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil',
                'password.symbols' => ':attribute harus memiliki setidaknya satu simbol',
                'password.numbers' => ':attribute harus memiliki setidaknya satu angka'
            ], [
                'first_name' => 'Nama Depan',
                'email' => 'Alamat Email',
                'password' => 'Kata Sandi',
                'password_confirmation' => 'Konfirmasi Kata Sandi'
            ]);

            if ($credential->fails()) {
                return $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                );
            }

            $user = $this->userService->getByEmail($request->email);

            if ($user) {
                return $this->responseData->create(
                    'Email sudah terdaftar!',
                    status: 'warning',
                    status_code: 403
                );
            }

            $user = $this->userService->save([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Mail::to($user->email)->send(new SuccessfullyRegistered($user));

            return $this->responseData->create(
                'Berhasil Membuat Akun!',
                [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'token' => auth()->guard('api')->login($user),
                ],
                status: 'success',
                status_code: 201
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                $e->getMessage(),
                status: 'error',
                status_code: 500,
            );
        }
    }

    public function signout()
    {
        Auth::guard('api')->logout();
        return $this->responseData->create(
            'Successfully Sign out!!',
        );
    }
}