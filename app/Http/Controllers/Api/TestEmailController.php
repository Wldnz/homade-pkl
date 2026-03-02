<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestEmailController extends Controller
{
    public function local(){
        $send = Mail::to(auth()->user()->email)->send(new TestMail());

        return response()->json([
            'status' => $send ? 'success' : 'false',
            'status_code' => $send ? 200 : 500
        ], $send ? 200 : 500);

    }
}
