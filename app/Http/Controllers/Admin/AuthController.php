<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ResponseData;
use App\Service\UserService;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Log;
use Validator;

class AuthController extends Controller
{
    private ResponseData $responseData;
    private UserService $userService;

    public function __construct() {
        $this->responseData = new ResponseData();
        $this->userService = new UserService();
    }

    public function signin(){
        return view('admin.auth.signin');
    }

    public function signinHandler(Request $request){
        try{
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

            if($credential->fails()){
                $response =  $this->responseData->create(
                    'Data Belum Valid',
                    errors : $credential->errors()->toArray(),
                    status:'warning',
                    status_code: 422,
                    isJson: false
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $user = $this->userService->getByEmail($request->email, true);

            // return $user ? $user->password : 'a';

            if(!$user || !Hash::check($request->password, $user->password)){
                $response =  $this->responseData->create(
                    'Tidak dapat menemukan email atau mencocokan password!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->userService->login($user);
            // mengirimkan email kalo ada yang login akun owner atau admin?
            $response = $this->responseData->create(
                'Berhasil Masuk, Kredential Valid!',
                isJson:false
            );

            // langsung ke halaman sebelumnya atau ke dashboard?
            return redirect()->route('admin.dashboard')->with(compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
             $response =  $this->responseData->create(
                    'Telah Terjadi Kesalahan Pada Server',
                    status:'error',
                    status_code: 500,
                    isJson: false
                );
                return redirect()->back()->withInput()->with(compact('response'));
        }
    }
}
