<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $this->responseData = new ResponseData();
        $this->userAddressService = new UserAddressService();
    }

    public function address()
    {
        try {
            $address = $this->userAddressService->all();

            if ($address->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan alamat pengguna',
                    status: 'warning',
                    status_code: 404
                );
            }

            return $this->responseData->create(
                'Berhasil menemukan alamat pengguna',
                UserAddressResource::collection($address)
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

    public function detail(string $id)
    {
        try {
            $address = $this->userAddressService->byID($id);

            if (!$address) {
                return $this->responseData->create(
                    'Tidak dapat menemukan alamat pengguna',
                    status: 'warning',
                    status_code: 404
                );
            }

            return $this->responseData->create(
                'Berhasil menemukan detail alamat pengguna',
                new DetailUserAddressResource($address)
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

    public function store(Request $request)
    {
        try {

            $validate = Validator::make($request->all(), [
                'fullname' => 'string|required|min:3',
                'phone' => 'phone:ID,mobile|required|min:8|max:15',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'numeric|required|decimal:-180,180|min:-180|max:180',
                'latitude' => 'numeric|required|decimal:-90,90|min:-90|max:90',
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
                return $this->responseData->create(
                    'Data Belum Valid',
                    errors: $validate->errors()->toArray(),
                    status: 'warning',
                    status_code: 422
                );
            }

            // cek terlebih dahulu apakah alamatnya sudah ada lebih dari maximal yang sudah ditentukan?
            $address = $this->userAddressService->all();

            $address_limit = (int) env('MAXIMAL_LIMIT_CUSTOMER_ADDRESS', 3);

            if ($address->count() >= $address_limit) {
                return $this->responseData->create(
                    "Maaf, Tidak Bisa Membuat Alamat Pengiriman Lebih Dari $address_limit",
                    status: 'warning',
                    status_code: 400
                );
            }

            $is_main_address = isset($request->is_main_address) && gettype($request->is_main_address) == 'boolean' && $request->is_main_address;

            $address = $this->userAddressService->saveAndChangeTheMainAdress([
                'id_user' => auth()->user()->id,
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'is_main_address' => $is_main_address
            ]);

            return $this->responseData->create(
                'Berhasil Membuat Alamat Email',
                new UserAddressResource($address),
                status_code:201
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }

    public function edit(Request $request, string $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                              'fullname' => 'string|required|min:3',
                'phone' => 'phone:ID,mobile|required|min:8|max:15',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'numeric|required|decimal:-180,180|min:-180|max:180',
                'latitude' => 'numeric|required|decimal:-90,90|min:-90|max:90',
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
                return $this->responseData->create(
                    'Data Belum Valid',
                    errors: $validate->errors()->toArray(),
                    status: 'warning',
                    status_code: 422
                );
            }

            $address = $this->userAddressService->byID($id);

            if (!$address) {
                return $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404
                );
            }

            $is_main_address = isset($request->is_main_address) && gettype($request->is_main_address) == 'boolean' && $request->is_main_address;

            $address = $this->userAddressService->updateAndChangeTheMainAddress($address, [
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'is_main_address' => $is_main_address
            ]);

            return $this->responseData->create(
                'Berhasil mengubah data',
                new DetailUserAddressResource($address),
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }
    public function remove(string $id)
    {
        try {

            $address = $this->userAddressService->byID($id);

            if (!$address) {
                return $this->responseData->create(
                    'Tidak Dapat Menemukan Alamat!',
                    status: 'warning',
                    status_code: 404
                );
            }

            $this->userAddressService->remove($address);

            return $this->responseData->create(
                'Berhasil Menghapus Data',
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
        }
    }

}
