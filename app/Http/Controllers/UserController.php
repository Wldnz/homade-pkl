<?php

namespace App\Http\Controllers;

use App\ResponseData;
use App\Service\UserService;


class UserController extends Controller
{
    private UserService $userService;
    private ResponseData $responseData;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->userService = new UserService();
    }

    public function me(){
        // data with address
        $response = $this->responseData->create(
            'Berhasil Mendapatkan Data Akun',
            [
                'user' => $this->userService->currentUser(),
            ]
        );
        return $response;
        // return view('me', $response);
    }


}
