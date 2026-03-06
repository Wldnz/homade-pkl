<?php

namespace App\Http\Controllers;

use App\Mail\SuccessfullyChangedPassword;
use App\Mail\SuccessfullyRegistered;
use App\Models\User;
use App\ResponseData;
use App\Service\UserService;
use Auth;
use Exception;
use Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Log;
use Mail;
use Str;
use Validator;
use function PHPUnit\Framework\isJson;

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
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus berupa email yang valid!',
                'min' => ':attribute minimal harus memiliki :min karater'
            ], [
                'email' => 'Alamat Email',
                'password' => 'Kata Sandi'
            ]);

            if ($credential->fails()) {
                $response = $this->responseData->create(
                    'Data Yang Dimasukkan Belum Valid Nih!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson:false
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
                $response =  $this->responseData->create(
                    'Data yang dimasukkan belum valid!',
                    errors: $credential->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson:false
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

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|string'
        ]);

        if ($validator->fails()) {
            // sementara gini dlu
            return 'Pastikan Semua Data Yang Diperlukan Dibawa!';
        }

        $user = $this->userService->getByEmail($request->email);

        if (!$user) {
            return 'Tidak dapat menemukan pengguna bang';
        }

        if (!Password::tokenExists($user, $request->token)) {
            return 'tokennya tidak valid atau sudah expired bang';
        }

        return view('reset-password', [
            'user' => $user,
            'token' => $request->token
        ]);
    }

    public function resetHandler(Request $request, string $token)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => ['required', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
                'password_confirmation' => 'required|same:password'
            ], [
                'required' => ':attribute dibutuhkan!',
                'email' => ':attribute harus memiliki email yang valid!',
                'min' => ':attribute minimal harus memiliki :min karater',
                'same' => ':attribute harus sama dengan :other',
                'password.mixed' => ':attribute harus memiliki setidaknya satu huruf besar dan satu huruf kecil',
                'password.symbols' => ':attribute harus memiliki setidaknya satu simbol',
                'password.numbers' => ':attribute harus memiliki setidaknya satu angka'
            ], [
                'email' => 'Alamat Email',
                'password' => 'Kata Sandi Baru',
                'password_confirmation' => 'Konfirmasi Kata Sandi Baru'
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
                [ ...$request->only('email', 'password', 'password_confirmation'), ...[ 'token' => $token ] ],
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
