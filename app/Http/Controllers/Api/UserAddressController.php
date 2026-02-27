<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailUserAddressResource;
use App\Http\Resources\UserAddressResource;
use App\ResponseData;
use App\Service\AchievementService;
use App\Service\PartnerService;
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
                'phone' => 'string|required|min:8',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'string|required|min:8',
                'latitude' => 'string|required|min:8',
            ]);

            $input = [
                'fullname' => $request->fullname ?? '',
                'phone' => $request->phone ?? '',
                'label' => $request->label ?? '',
                'address' => $request->address ?? '',
                'longitude' => $request->longitude ?? '',
                'latitude' => $request->latitude ?? '',
            ];

            if ($validate->fails()) {
                return $this->responseData->create(
                    'Data Belum Valid',
                    [
                        'errors' => $validate->errors(),
                        'input' => $input,
                    ],
                    status: 'warning',
                    status_code: 422
                );
            }

            // cek terlebih dahulu apakah alamatnya sudah ada 3?
            $address = $this->userAddressService->all();

            if ($address->count() >= 3) {
                return $this->responseData->create(
                    'Maaf, Tidak Bisa Membuat Alamat Pengiriman Lebih Dari 3',
                    status: 'warning',
                    status_code: 403
                );
            }

            $address = $this->userAddressService->save([
                'id_user' => auth()->user()->id,
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
            ]);

            return $this->responseData->create(
                'Berhasil Membuat Alamat Email',
                new UserAddressResource($address),
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
                'phone' => 'string|required|min:8',
                'label' => 'string|required|min:3',
                'address' => 'string|required|min:8',
                'longitude' => 'string|required|min:8',
                'latitude' => 'string|required|min:8',
            ]);

            $input = [
                'fullname' => $request->fullname ?? '',
                'phone' => $request->phone ?? '',
                'label' => $request->label ?? '',
                'address' => $request->address ?? '',
                'longitude' => $request->longitude ?? '',
                'latitude' => $request->latitude ?? '',
            ];

            if ($validate->fails()) {
                return $this->responseData->create(
                    'Data Belum Valid',
                    [
                        'errors' => $validate->errors(),
                        'input' => $input,
                    ],
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

            $this->userAddressService->update($address, [
                'received_name' => $request->fullname,
                'phone' => $request->phone,
                'label' => $request->label,
                'address' => $request->address,
                'note' => $request->note ?? '',
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
            ]);

            // langsung success aja dlu, apa perlu validasi lagi?
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

            $address->delete();

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
