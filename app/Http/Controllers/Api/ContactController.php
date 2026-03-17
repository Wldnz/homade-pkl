<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\ResponseData;
use App\Service\ContactService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;
use Validator;

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


    public function sendEmailToSupport(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'fullname' => 'required|string|min:3',
                'email' => 'required|email',
                'subject' => 'required|string|min:3',
                'message' => 'required|string|min:10',
            ], [
                'required' => "Pastikan :attribute sudah dikirimkan",
                'string' => ':attribute harus berupa sebuah text',
                'email' => ':attribute harus berupa email yang valid',
                'min' => ':attribute harus memiliki minimal :min'
            ], [
                'fullname' => 'Nama Lengkap',
                'email' => 'Alamat Email',
                'subject' => 'Subjek',
                'message' => 'Pesan'
            ]);

            if ($validator->fails()) {
                return $this->responseData->create(
                    'Data yang diberikan belum valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
            }

            $contactService = new ContactService();
            $contact = $contactService->contact();

            if (!$contact || empty($contact->email)) {
                return $this->responseData->create(
                    'Maaf, Perusahaan belum menaruh alamat email untuk kontak support',
                    status: 'error',
                    status_code: 404,
                    isJson: false,
                );
            }

            $mailData = [
                'fullname' => $request->fullname,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            Mail::to($contact->email)->send(new \App\Mail\ContactSupport($mailData));

            return $this->responseData->create(
                'Pesan berhasil dikirim! Tim Support akan segera menghubungi Anda.',
                isJson: false,
            );

        } catch (Exception $e) {
            return $this->responseData->create(
                'Terjadi kesalahan saat mengirim email: ' . $e->getMessage(),
                status: 'error',
                status_code: 500,
                isJson: false,
            );
        }
    }
}
