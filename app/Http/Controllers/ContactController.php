<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\ResponseData;
use App\Service\ContactService;
use Exception;
use Illuminate\Http\Request;
use Log;
use function PHPUnit\Framework\isJson;

class ContactController extends Controller
{
    private ResponseData $responseData;
    private ContactService $contactService;

    public function __construct() {
        $this->responseData = new ResponseData();
        $this->contactService = new ContactService();
    }

    public function contact(){
        try{

            $contact = $this->contactService->information();

            if(!$contact){
                $response =  $this->responseData->create(
                    'Tidak dapat menemukan informasi kontak',
                    status:'warning',
                    status_code:404,
                    isJson:false
                );
                return view('contact', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan data',
                new ContactResource($contact),
                isJson:false
            );

            return view('contact', compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status:'error',
                status_code:500,
                isJson:false
            );
            return view('contact', compact('response'));
        }
    }

}
