<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\ResponseData;
use App\Service\UserService;
use Auth;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Log;
use Validator;

class AuthController extends Controller
{

    private ResponseData $responseData;

    private UserService $userService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->userService = new UserService();
    }

    public function signin(){
        return view('testing.signin');
    }

    public function signup(){
        return view('testing.signup');
    }
    
    public function authorized(){
        return view('testing.authorized');
    }
}
