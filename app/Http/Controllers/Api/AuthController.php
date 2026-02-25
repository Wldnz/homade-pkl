<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\ResponseData;
use App\Service\UserService;
use Auth;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Log;
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
            ]);

            $data = [
                'email' => $request->email ?? '',
                'password' => $request->password ?? ''
            ];

            if ($credential->fails()) {
                return $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid Nih!',
                    [
                        'errors' => $credential->errors(),
                        'input' => $data
                    ],
                    status: 'warning',
                    status_code: 422,
                );
            }

            $user = $this->userService->getByEmail($request->email);

            if (!$user) {
                return $this->responseData->create(
                    'Tidak dapat menemukan email',
                    $data,
                    status: 'warning',
                    status_code: 404,
                );
            }

            if (!Hash::check($request->password, $user->password)) {
                return $this->responseData->create(
                    'Password Yang Anda Masukkan Tidak Valid!',
                    $data,
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
                'first_name' => 'required|min:3',
                'email' => 'required|min:8|email',
                'password' => 'required|min:8'
            ]);

            $data = [
                'first_name' => $request->first_name ?? '',
                'last_name' => $request->last_name ?? '',
                'email' => $request->email ?? '',
                'password' => $request->password ?? ''
            ];

            if ($credential->fails()) {
                return $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    [
                        'errors' => $credential->errors(),
                        'input' => $data
                    ],
                    status: 'warning',
                    status_code: 422,
                );
            }

            $user = $this->userService->getByEmail($request->email);

            if ($user) {
                return $this->responseData->create(
                    'Email sudah terdaftar!',
                    $data,
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
        $this->userService->logout();
        return $this->responseData->create(
            'Successfully Sign out!!',
        );
    }
}