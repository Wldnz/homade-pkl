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

    public function signin()
    {
        return view('signin');
    }

    public function signup()
    {
        return view('signup');
    }

    public function signinHandler(Request $request)
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
                $response = $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid Nih!',
                    [
                        'errors' => $credential->errors(),
                        'input' => $data
                    ],
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );
                return redirect()->back()->with(compact('response'));
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan email',
                    $data,
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );
                return redirect()->back()->with(compact('response'));
            }

            if (!Hash::check($request->password, $user->password)) {
                $response = $this->responseData->create(
                    'Password Yang Anda Masukkan Tidak Valid!',
                    $data,
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );
                return redirect()->back()->with(compact('response'));
            }

            Log::alert('login sebagai' . $user->first_name);

            $this->userService->login($user);
            session()->regenerate();
            //sementatara return ke dashboard dlu ya...
            return redirect()->to($request->query('redirect_url', '/'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return redirect()->back()->with(compact('response'));
        }
    }

    public function signupHandler(Request $request)
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
                $response = $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    [
                        'errors' => $credential->errors(),
                        'input' => $data
                    ],
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );
                return redirect()->back()->with(compact('response'));
            }

            $user = $this->userService->getByEmail($request->email);

            if ($user) {
                $response = $this->responseData->create(
                    'Email sudah terdaftar!',
                    [
                        'errors' => $credential->errors(),
                        'input' => $data
                    ],
                    status: 'warning',
                    status_code: 403,
                    isJson: false
                );
                return redirect()->back()->with(compact('response'));
            }

            $user = $this->userService->save([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $response = $this->responseData->create(
                'Berhasil Membuat Akun!',
                [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ],
                status: 'success',
                status_code: 201,
                isJson: false,
            );

            $this->userService->login($user);
            session()->regenerate();

            return redirect()->to($request->query('redirect_url', '/'))->with($response);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                $e->getMessage(),
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return redirect()->back()->with(compact('response'));
        }
    }

    public function signout()
    {
        $this->userService->logout();
        $response = $this->responseData->create(
            'Berhasil keluar sesi!',
            isJson: false
        );
        return redirect('/')->with(compact('response'));
    }
}
