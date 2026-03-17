<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailUserAddressResource;
use App\Http\Resources\UserAddressResource;
use App\ResponseData;
use App\Service\UserAddressService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class UserAddressController extends Controller
{
    private ResponseData $responseData;

    private UserAddressService $userAddressService;

    public function __construct()
    {
        $this->responseData = new ResponseData;
        $this->userAddressService = new UserAddressService;
    }

    public function address(Request $request)
    {
        try {
            $address = $this->userAddressService->all();
            if ($address->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan alamat pengguna',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return view('profile.address.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil menemukan alamat pengguna',
                UserAddressResource::collection($address)->toArray($request),
                isJson: false
            );

            return view('profile.address.index', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('profile.address.index', compact('response'));
        }
    }

    public function detail(Request $request, string $id)
    {
        try {

            $address = $this->userAddressService->byID($id);

            if (!$address) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return view('profile.address.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Menemukan Detail Alamat Email',
                (new DetailUserAddressResource($address))->toArray($request),
                isJson: false
            );

            return view('profile.address.detail', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );

            return view('profile.address.detail', compact('response'));
        }
    }

    public function store()
    {
        return view('profile.address.store');
    }

    public function storeHandler(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'fullname' => 'string|required|min:3',
                'phone' => 'phone:ID,mobile|required|min:8',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'numeric|required|decimal:-180,180|min:-180|max:180',
                'latitude' => 'numeric|required|decimal:-90,90|min:-90|max:90',
                
            ], [
                'required' => ':attribute dibutuhkan!',
                'min' => ':attribute minimal harus memiliki minimal :min karakter',
                'max' => ':attribute minimal harus memiliki maximal :max karakter',
                'phone' => ':attribute harus valid ya',
                'numeric' => ':attribute harus numeric',
                'decimal' => ':attribute adalah sebuah decimal minimal penempatan place adalah :decimal'
            ], [
                'fullname' => 'Nama Penerima',
                'phone' => 'Nomor Telepon',
                'label' => 'Label',
                'address' => 'Alamat',
            ]);

            if ($validate->fails()) {
                return $response = $this->responseData->create(
                    'Data Belum Valid',
                    errors: $validate->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            // cek terlebih dahulu apakah alamatnya sudah ada lebih dari maximal yang sudah ditentukan?
            $address = $this->userAddressService->all();

            $address_limit = (int) env('MAXIMAL_LIMIT_CUSTOMER_ADDRESS', 3);

            if ($address->count() >= $address_limit) {
                $response = $this->responseData->create(
                    "Maaf, Tidak Bisa Membuat Alamat Pengiriman Lebih Dari $address_limit",
                    status: 'warning',
                    status_code: 403,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $is_main_address = false;

            if (isset($request->is_main_address)) {
                $is_main_address = (bool) $request->is_main_address;
            }

            $address = $this->userAddressService->saveAndChangeTheMainAdress([
                'id_user' => auth()->user()->id,
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'is_main_address' => $is_main_address,
            ]);

            $response = $this->responseData->create(
                'Berhasil Membuat Alamat Email',
                (new UserAddressResource($address))->toArray($request),
                isJson: false,
            );

            return redirect()->route('user.user-address')->with(compact('response'));

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

    public function editHandler(Request $request, string $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'fullname' => 'string|required|min:3',
                'phone' => 'phone:ID,mobile|required|min:8|max:15',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'numeric|required|decimal:-180,180|min:-180|max:180',
                'latitude' => 'numeric|required|decimal:-90,90|min:-90|max:90',
                'is_main_address' => ''
            ], [
                'required' => ':attribute dibutuhkan!',
                'min' => ':attribute minimal harus memiliki minimal :min karakter',
                'max' => ':attribute minimal harus memiliki maximal :max karakter',
                'phone' => ':attribute harus valid ya',
                'decimal' => ':attribute adalah sebuah decimal minimal penempatan place adalah :decimal'
            ], [
                'fullname' => 'Nama Penerima',
                'phone' => 'Nomor Telepon',
                'label' => 'Label',
                'address' => 'Alamat',
            ]);

            if ($validate->fails()) {
                $response =  $this->responseData->create(
                    'Data Belum Valid',
                    errors: $validate->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $address = $this->userAddressService->byID($id);

            if (!$address) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            $is_main_address = false;

            if (isset($request->is_main_address)) {
                $is_main_address = (bool) $request->is_main_address;
            }

            $address = $this->userAddressService->updateAndChangeTheMainAddress($address, [
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'is_main_address' => $is_main_address,
            ]);

            $response = $this->responseData->create(
                'Berhasil mengubah data',
                (new DetailUserAddressResource($address))->toArray($request),
                isJson: false,
            );

            return redirect()->route('user.user-address')->with(compact('response'));

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

    public function removeHandler(string $id)
    {
        try {
            $address = $this->userAddressService->byID($id);
            if (!$address) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );

                return redirect()->back()->with(compact('response'));
            }

            $this->userAddressService->remove($address);

            $response = $this->responseData->create(
                'Berhasil Menghapus Data',
                isJson: false,
            );

            return redirect()->route('user.user-address')->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );

            return redirect()->back()->with(compact('response'));
        }
    }
}
