<?php

namespace App\Http\Controllers;

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

    public function signin(){
        return view('signin');
    }

    public function signup(){
        return view('signup');
    }

    public function signinHandler(Request $request)
    {

        $credential = Validator::make($request->all(), [
            'email' => 'required|email|min:8',
            'password' => 'required|min:8'
        ]);

        if ($credential->fails()) {
            $response = $this->responseData->create(
                'Data Yang Dimasukkan Belum Valid Nih!',
                data: $credential->errors(),
                status: 'warning',
                status_code: 422,
                isJson:false
            );
            return redirect()->back()->with($response);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $response = $this->responseData->create(
                'Tidak dapat menemukan email',
                status: 'warning',
                status_code: 404,
                isJson:false
            );
            return redirect()->back()->with( $response);
        }

        if (!Hash::check($request->password, $user->password)) {
            $response = $this->responseData->create(
                'Password Yang Anda Masukkan Tidak Valid!',
                status: 'warning',
                status_code: 404,
                isJson:false
            );
            return redirect()->back()->with($response);
        }

        Auth::login($user);
        session()->regenerate();
        //sementatara return ke dashboard dlu ya...
        return redirect()->to($request->query('redirect_url', '/'));
    }

     public function signupHandler(Request $request)
    {
        $credential = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'email' => 'required|min:8|email',
            'password' => 'required|min:8'
        ]);

        if ($credential->fails()) {
            $response = $this->responseData->create(
                'Data yang dimasukkan belum valid!',
                data: $credential->errors(),
                status: 'warning',
                status_code: 422,
                isJson:false
            );
            return redirect()->back()->with($response);
        }

        $user = $this->userService->getByEmail($request->email);

        if ($user) {
            $response =  $this->responseData->create(
                'Email sudah terdaftar!',
                status: 'warning',
                status_code: 403,
                isJson:false
            );
            return redirect()->back()->with($response); 
        }


        

        try {
            $this->userService->save([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user = $this->userService->getByEmail($request->email);

            if (!$user) {
                $response =  $this->responseData->create(
                    'Tidak dapat menemukan email',
                    status: 'warning',
                    status_code: 404,
                    isJson : false,
                );
                return redirect()->back()->with($response);
            }

            $response =  $this->responseData->create(
                'Berhasil Membuat Akun!',
                [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email'=> $user->email,
                ],
                status: 'success',
                status_code: 201,
                isJson:false,
            );

            Auth::login($user);
            session()->regenerate();

            return redirect()->to($request->query('redirect_url', '/'))->with($response);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                $e->getMessage(),
                status: 'error',
                status_code: 500,
            );
        }
    }

    public function signout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect('/');
    }
}
