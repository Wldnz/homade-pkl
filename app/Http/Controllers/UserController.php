<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeResources;
use App\ResponseData;
use App\Service\UserService;
use Exception;
use Log;


class UserController extends Controller
{
    private UserService $userService;
    private ResponseData $responseData;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->userService = new UserService();
    }

    public function me()
    {
        try {
            $user = $this->userService->currentUser();
            $response = $this->responseData->create(
                'Berhasil Mendapatkan Data Akun',
                new MeResources($user),
                isJson:false
            );
            return view('profile.me', compact('response'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $resonse = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status:'error',
                status_code:500,
                isJson:false
            );
            return view('profile.me', compact('response'));
        }
    }


}
