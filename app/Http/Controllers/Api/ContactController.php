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

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->contactService = new ContactService();
    }

    public function full()
    {
        try {
            $contact = $this->contactService->information();

            if (!$contact) {
                return $this->responseData->create(
                    'Tidak dapat menemukan informasi full informasi',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan data',
                new ContactResource($contact),
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

    public function operational()
    {
        try {
            $contact = $this->contactService->operational();

            if (!$contact) {
                return $this->responseData->create(
                    'Tidak dapat menemukan informasi jadwal operasional',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan jadwal operasional',
                $contact,
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

    public function contact()
    {
        try {
            $contact = $this->contactService->contact();

            if (!$contact) {
                return $this->responseData->create(
                    'Tidak dapat menemukan informasi kontak',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan informasi kontak',
                $contact,
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

    public function address()
    {
        try {
            $contact = $this->contactService->address();

            if (!$contact) {
                return $this->responseData->create(
                    'Tidak dapat menemukan informasi alamat perusahaan',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan data informasi alamat perusahaan',
                $contact,
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

    public function socialMedia()
    {
        try {
            $contact = $this->contactService->socialMedia();

            if (!$contact) {
                return $this->responseData->create(
                    'Tidak dapat menemukan informasi sosial media',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan sosial media',
                $contact,
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
