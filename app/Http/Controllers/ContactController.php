<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\ResponseData;
use App\Service\ContactService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Mail;
use Validator;
use function PHPUnit\Framework\isJson;

class ContactController extends Controller
{
    private ResponseData $responseData;
    private ContactService $contactService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->contactService = new ContactService();
    }

    public function contact(Request $request)
    {
        try {

            $contact = $this->contactService->information();

            if (!$contact) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan informasi kontak',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );
                return view('contact', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan data',
                (new ContactResource($contact))->toArray($request),
                isJson: false
            );

            return view('contact', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('contact', compact('response'));
        }
    }

    public function sendEmailToSupport(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'fullname' => 'required|string|min:1',
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
                $response = $this->responseData->create(
                    'Data yang diberikan belum valid',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $contactService = new ContactService();
            $contact = $contactService->contact();

            if (!$contact || empty($contact->email)) {
                $response = $this->responseData->create(
                    'Maaf, Perusahaan belum menaruh alamat email untuk kontak support',
                    status: 'error',
                    status_code: 404,
                    isJson: false,
                );
                return redirect()->back()->withInput()->with(compact('response'));
            }

            $mailData = [
                'fullname' => $request->fullname,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            Mail::to($contact->email)->send(new \App\Mail\ContactSupport($mailData));

            $response = $this->responseData->create(
                'Pesan berhasil dikirim! Tim Support akan segera menghubungi Anda.',
                isJson: false,
            );
            return redirect()->back()->with(compact('response'));

        } catch (Exception $e) {
            $response = $this->responseData->create(
                'Terjadi kesalahan saat mengirim email: ' . $e->getMessage(),
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

}
