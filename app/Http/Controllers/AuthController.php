<?php

namespace App\Http\Controllers;

use App\Mail\SuccessfullyChangedPassword;
use App\Mail\SuccessfullyRegistered;
use App\Models\User;
use App\ResponseData;
use App\Service\UserService;
use Exception;
use Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Log;
use Mail;
use Str;
use Validator;

class AuthController extends Controller
{
    private ResponseData $responseData;

    private UserService $userService;

    public function __construct()
    {
        $this->responseData = new ResponseData;
        $this->userService = new UserService;
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
                'password' => 'required|min:8',
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus berupa email yang valid!',
                'min' => ':attribute minimal harus memiliki :min karater',
            ], [
                'email' => 'Alamat Email',
                'password' => 'Kata Sandi',
            ]);

            if ($credential->fails()) {
                $response = $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid Nih!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            if ($credential->fails()) {
                $response = $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid Nih!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan email',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            if (!Hash::check($request->password, $user->password)) {
                $response = $this->responseData->create(
                    'Password Yang Anda Masukkan Tidak Valid!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->userService->login($user);
            session()->regenerate();

            // sementatara return ke dashboard dlu ya...
            return redirect()->to($request->query('redirect_url', '/'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function signupHandler(Request $request)
    {
        try {

            $credential = Validator::make($request->all(), [
                'first_name' => 'required|min:2',
                'email' => 'required|min:8|email',
                'password' => [
                    'required',
                    \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
                ],
                'password_confirmation' => 'required|same:password',
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus berupa email yang valid!',
                'min' => ':attribute minimal harus memiliki :min karater',
                'same' => ':attribute harus sama dengan :other',
                'password.mixed' => ':attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil',
                'password.symbols' => ':attribute harus memiliki setidaknya satu simbol',
                'password.numbers' => ':attribute harus memiliki setidaknya satu angka',
            ], [
                'first_name' => 'Nama Depan',
                'email' => 'Alamat Email',
                'password' => 'Kata Sandi',
                'password_confirmation' => 'Konfirmasi Kata Sandi',
            ]);

            if ($credential->fails()) {
                $response = $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $user = $this->userService->getByEmail($request->email);

            if ($user) {
                $response = $this->responseData->create(
                    'Email sudah terdaftar!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 403,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
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

            Mail::to($user->email)->send(new SuccessfullyRegistered($user));

            $this->userService->login($user);
            session()->regenerate();

            return redirect()->to($request->query('redirect_url', '/'))->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                $e->getMessage(),
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function forgot()
    {
        return view('forgot-password');
    }

    public function forgotHandler(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                ['email' => 'email|required|min:3'],
                [
                    'required' => ':attribute dibutuhkan!',
                    'email' => ':attribute membutuhkan email yang valid',
                    'min' => ':attribute membutuhkan minimal :min karakter',
                ],
                [
                    'email' => 'Alamat Email',
                ]
            );

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $user = $this->userService->getByEmail($request->email);

            if (!$user) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat Email',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $isSent = Password::sendResetLink(
                $request->only('email')
            );

            if (!$isSent == Password::ResetLinkSent) {
                $response = $this->responseData->create(
                    'Tidak Dapat Mengirimkan Notifikasi Ke Email',
                    status: 'warning',
                    status_code: 400, // ini apa yang enak ya status codenya ??
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mengirimakn Reset Kata Sandi Pada Email',
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function changePassword(Request $request){
        return view('profile.change-password');
    }

    public function changePasswordHandler(Request $request)
    {
        try {

            $credential = Validator::make($request->all(), [
                'old_password' => 'required|string|min:8',
                'password' => [
                    'required',
                    \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
                ],
                'password_confirmation' => 'required|same:password',
            ], [
                'required' => ':attribute dibutuhkan!',
                'min' => ':attribute minimal harus memiliki :min karater',
                'same' => ':attribute harus sama dengan :other',
                'password.mixed' => ':attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil',
                'password.symbols' => ':attribute harus memiliki setidaknya satu simbol',
                'password.numbers' => ':attribute harus memiliki setidaknya satu angka',
            ], [
                'old_password' => 'Kata Sandi Lama',
                'password' => 'Kata Sandi Baru',
                'password_confirmation' => 'Konfirmasi Kata Sandi Baru',
            ]);

            if ($credential->fails()) {
                $response = $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson:false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                $response = $this->responseData->create(
                    'Pastikan memasukkan kata sandi yang benar!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            if ($request->old_password == $request->password) {
                $response = $this->responseData->create(
                    'Pastikan kata sandi yang dimasukkan tidak sama dengan kata sandi yang lama!',
                    status: 'warning',
                    status_code: 400,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $this->userService->changePassword($user, Hash::make($request->password));

            Mail::to($user->email)->send(new SuccessfullyChangedPassword($user));

            $response = $this->responseData->create(
                'Berhasil Mengganti Kata Sandi Lama Dengan Kata Sandi Baru',
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|string',
        ]);

        if ($validator->fails()) {
            $response = $this->responseData->create(
                'Data yang dimasukkan belum valid!',
                errors: $validator->errors()->toArray(),
                status: 'warning',
                status_code: 422,
                isJson: false,
            );

            return view('reset-password', compact('response'));
        }

        $user = $this->userService->getByEmail($request->email);

        if (!$user) {
            $response = $this->responseData->create(
                'Tidak Dapat Menemukan Pengguna',
                status: 'warning',
                status_code: 404,
                isJson: false,
            );

            return view('reset-password', compact('response'));
        }

        if (!Password::tokenExists($user, $request->token)) {
            $response = $this->responseData->create(
                'Tidak Dapat Menemukan Token Atau Token Sudah Kadaluarsa',
                status: 'warning',
                status_code: 400,
                isJson: false,
            );

            return view('reset-password', compact('response'));
        }

        $response = $this->responseData->create(
            'Berhasil Dalam Memverifikasi Data Pengguna Untuk Reset Kata Sandi',
            [
                'user' => $user,
                'token' => $request->token,
            ],
            isJson: false,
        );
        return view('reset-password', compact('response'));
    }

    public function resetHandler(Request $request, string $token)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => ['required', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
                'password_confirmation' => 'required|same:password',
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus memiliki email yang valid!',
                'min' => ':attribute minimal harus memiliki :min karater',
                'same' => ':attribute harus sama dengan :other',
                'password.mixed' => ':attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil',
                'password.symbols' => ':attribute harus memiliki setidaknya satu simbol',
                'password.numbers' => ':attribute harus memiliki setidaknya satu angka',
            ], [
                'email' => 'Alamat Email',
                'password' => 'Kata Sandi Baru',
                'password_confirmation' => 'Konfirmasi Kata Sandi Baru',
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid!',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $user = $this->userService->getByEmail($request->email);

            if (!$user) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Pengguna Dari Alamat Email Yang Diberikan!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            // check password lama dan baru
            if (Hash::check($request->password, $user->password)) {
                $response = $this->responseData->create(
                    'Kata Sandi Baru Tidak Boleh Sama Dengan Kata Sandi Lama',
                    status: 'warning',
                    status_code: 400,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $status = Password::reset(
                [...$request->only('email', 'password', 'password_confirmation'), ...['token' => $token]],
                function (User $user, string $password) {

                    $user->password = Hash::make($password);
                    $user->setRememberToken(str::random(60));
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status != Password::PASSWORD_RESET) {
                $response = $this->responseData->create(
                    'Tidak Berhasil Melakukan Reset Kata Sandi, Periksa Kembali Token Yang Diberikan Kemungkinan Tidak Valid Atau Sudah Expired!',
                    status: 'warning',
                    status_code: 400, // bingung mau naruh apa
                    isJson: false,
                );

                return redirect()->back()->with(compact('response'));
            }

            Mail::to($user->email)->send(new SuccessfullyChangedPassword($user));

            $response = $this->responseData->create(
                'Berhasil Melakukan Reset Kata Sandi',
                isJson: false,
            );

            // perlu redirect back atau buat halaman baru atau ke login?
            return redirect()->route('user.signin')->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return redirect()->back()->withInput()->with(compact('response'));
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
