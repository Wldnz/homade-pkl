<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeResources;
use App\ResponseData;
use App\Service\UserService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;
use function PHPUnit\Framework\isJson;


class UserController extends Controller
{
    private UserService $userService;
    private ResponseData $responseData;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->userService = new UserService();
    }

    public function me(Request $request)
    {
        try {
            $user = $this->userService->currentUser();
            $response = $this->responseData->create(
                'Berhasil Mendapatkan Data Akun',
                (new MeResources($user))->toArray($request),
                isJson: false
            );
            return view('profile.index', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $resonse = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('profile.index', compact('response'));
        }
    }

    public function edit(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|min:1',
                'last_name' => 'string',
                'phone' => 'required|phone:mobile,ID|min:8|max:15',
                'email' => 'required|email|min:3',
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
                $response = $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
            }

            $user = $this->userService->currentUser();

            if(!$user){
                $response = $this->responseData->create(
                    'Tidak dapat menemukan pengguna',
                    status:'warning',
                    status_code: 404,
                    isJson:false,   
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->userService->edit($user, $request->only('first_name', 'last_name', 'phone'));

            $response = $this->responseData->create(
                'Berhasil dalam merubah data',
                isJson:false,
            );
            
            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response  =$this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson:false
            );    
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }


}
