<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MeResources;
use App\ResponseData;
use App\Service\UserService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Propaganistas\LaravelPhone\Rules\Phone;
use Validator;


class UserController extends Controller
{

    private UserService $userService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->responseData = new ResponseData();
    }

    public function me()
    {
        try {
            $user = $this->userService->currentUser();
            return $this->responseData->create(
                'Succesfully getting data',
                new MeResources($user),
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah terjadi kesalaha pada server',
                status: 'error',
                status_code: 500,
            );
        }
    }

    public function edit(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|min:3',
                'last_name' => 'string',
                'phone' => 'required|phone:mobile,ID|min:8|max:15',
                'email' => 'email',
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus memiliki email yang valid!',
                'min' => ':attribute harus memiliki minimal :min',
                'max' => ':attribute harus memiliki maximal :max',
                'phone' => ':attribute kurang benar, silahkan cek kembali!'
            ], [
                'first_name' => 'Nama Depan',
                'phone' => 'Nomor Telepon',
                'email' => 'Alamat Email'
            ]);

            if ($validator->fails()) {
                return $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422
                );
            }

            $user = auth()->user();

            $this->userService->edit($user, [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email ?? $user->email,
                'phone' => $request->phone
            ]);

            return $this->responseData->create(
                'Berhasil Mengubah Data Pengguna',
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

}
