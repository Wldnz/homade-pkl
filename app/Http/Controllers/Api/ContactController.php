<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\ResponseData;
use App\Service\ContactService;
use Exception;
use Log;

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
            // ini mungkin bakal di pisah, ada yang mendapatkan full informasi, sosial-media, alamt, dll
            $contact = $this->contactService->information();

            if(!$contact){
                return $this->responseData->create(
                    'Tidak dapat menemukan informasi kontak',
                    status:'warning',
                    status_code:404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan data',
                new ContactResource($contact),
            );


        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status:'error',
                status_code:500,
            );
        }
    }

}
