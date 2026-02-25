<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $this->userService = new UserService();
        $this->responseData = new ResponseData();
    }

    public function me()
    {
        try {
            $user = $this->userService->currentUser();
            return $this->responseData->create(
                'Succesfully getting data',
                new MeResources($user),
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah terjadi kesalaha pada server',
                status:'error',
                status_code: 500,
            );
        }
    }

}
