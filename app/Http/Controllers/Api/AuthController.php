<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SuccessfullyChangedPassword;
use App\Mail\SuccessfullyRegistered;
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

    public function forgot(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                ['email' => 'email|required|min:3'],
                [
                    'required' => ':attribute dibutuhkan!',
                    'email' => ':attribute membutuhkan email yang valid',
                    'min' => ':attribute membutuhkan minimal :min karakter'
                ],
                [
                    'email' => 'Alamat Email'
                ]
            );

            if ($validator->fails()) {
                return $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422
                );
            }

            $user = $this->userService->getByEmail($request->email);

            if (!$user) {
                return $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat Email',
                    status: 'warning',
                    status_code: 404
                );
            }

            $isSent = \Illuminate\Support\Facades\Password::sendResetLink(
                $request->only('email')
            );

            if (!$isSent == \Illuminate\Support\Facades\Password::ResetLinkSent) {
                return $this->responseData->create(
                    'Tidak Dapat Mengirimkan Notifikasi Ke Email',
                    status: 'warning',
                    status_code: 404// ini apa yang enak ya status codenya ??
                );
            }

            return $this->responseData->create(
                'Berhasil Mengirimakn Forgot Password',
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }

    // kemungikannini tidak di pakai, karena langsung ke websitenya
    public function reset(Request $request)
    {
        try {

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }

    public function change(Request $request)
    {
        try {

            $credential = Validator::make($request->all(), [
                'old_password' => 'required|string|min:8',
                'password' => [
                    'required',
                    Password::min(8)->mixedCase()->numbers()->symbols(),
                ],
                'password_confirmation' => 'required|same:password'
            ], [
                'required' => ':attribute dibutuhkan!',
                'min' => ':attribute minimal harus memiliki :min karater',
                'same' => ':attribute harus sama dengan :other',
                'password.mixed' => ':attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil',
                'password.symbols' => ':attribute harus memiliki setidaknya satu simbol',
                'password.numbers' => ':attribute harus memiliki setidaknya satu angka'
            ], [
                'old_password' => 'Kata Sandi Lama',
                'password' => 'Kata Sandi Baru',
                'password_confirmation' => 'Konfirmasi Kata Sandi Baru'
            ]);

            if ($credential->fails()) {
                return $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                );
            }

            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->responseData->create(
                    'Pastikan memasukkan kata sandi yang benar!',
                    status: 'warning',
                    status_code: 404,
                );
            }

            if ($request->old_password == $request->password) {
                return $this->responseData->create(
                    'Pastikan kata sandi yang dimasukkan tidak sama dengan kata sandi yang lama!',
                    status: 'warning',
                    status_code: 400,
                );
            }

            $this->userService->edit($user, [
                'password' => Hash::make($request->password)
            ]);

            Mail::to($user->email)->send(new SuccessfullyChangedPassword($user));

            return $this->responseData->create(
                'Berhasil Mengganti Kata Sandi Lama Dengan Kata Sandi Baru'
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }
}