<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\ResponseData;
use App\Service\UserService;


class UserController extends Controller
{

    private UserService $userService;

    private ResponseData $responseData;

    public function __construct() {
        $this->userService = new UserService();
        $this->responseData = new ResponseData();
    }

    public function me(){
        return $this->responseData->create(
            'Succesfully getting data',
            $this->userService->currentUser(),
        );
    }

}
