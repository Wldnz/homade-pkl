<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\ResponseData;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{

    private ResponseData $responseData;

    public function __construct()
    {
        $this->responseData = new ResponseData();
    }

    public function signin(Request $request)
    {

        $credential = Validator::make($request->all(), [
            'email' => 'required|email|min:8',
            'password' => 'required|min:8'
        ]);

        if ($credential->fails()) {
            return $this->responseData->create(
                'Data Yang Dimasukkan Belum Valid Nih!',
                data: $credential->errors(),
                status: 'warning',
                status_code: 422,
            );
        }

        $user = User::where('email', $request->email)->first();

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

        Auth::login($user);

        return $this->responseData->create(
            'Berhasil Masuk!',
        );

    }

}