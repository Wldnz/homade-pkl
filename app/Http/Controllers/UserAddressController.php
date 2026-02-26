<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailUserAddressResource;
use App\Http\Resources\UserAddressResource;
use App\ResponseData;
use App\Service\UserAddressService;
use Exception;
use Log;


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
               $response =  $this->responseData->create(
                    'Tidak dapat menemukan alamat pengguna',
                    status: 'warning',
                    status_code: 404,
                    isJson:false
                );
                return view('profile.address', compact('address'));
            }

            $response = $this->responseData->create(
                'Berhasil menemukan alamat pengguna',
                UserAddressResource::collection($address),
                isJson: false
            );

            return view('profile.address', compact('address'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return view('profile.address', compact('address'));
        }
    }


    public function store()
    {
    }

    public function edit()
    {
    }
    public function remove()
    {
    }

}
