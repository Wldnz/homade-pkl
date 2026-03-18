<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ResponseData;
use App\Service\ContactService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class SettingController extends Controller
{
    private ContactService $contactService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->contactService = new ContactService;
        $this->responseData = new ResponseData;
    }

    public function index()
    {

        $setting = $this->contactService->information();

        if (! $setting) {
            $response = $this->responseData->create(
                'Tidak dapat menemukan setting',
                status: 'warning',
                status_code: 404,
                isJson: false,
            );

            return view('admin.setting.index', compact('response'));
        }

        $response = $this->responseData->create(
            'Berhasil mendapatkan detail data',
            $setting,
            isJson: false,
        );

        return view('admin.setting.index', compact('response'));

    }

    public function editHandler(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // Data Wajib
                'app_name' => 'required|string|min:1',
                'email' => 'required|email',
                'customer_care_phone' => 'required|numeric',
                'address' => 'required|string',
                'open_hours_at' => 'required',
                'close_hours_at' => 'required',
                'operating_days_info' => 'required|string',

                // Data Opsional (Sosmed & Kordinat)
                'shipping_fee_per_km' => 'nullable|numeric',
                'tiktok_url' => 'nullable|url',
                'youtube_url' => 'nullable|url',
                'facebook_url' => 'nullable|url',
                'instagram_url' => 'nullable|url',
                'x_url' => 'nullable|url',
                'longitude' => 'nullable|numeric',
                'latitude' => 'nullable|numeric',
            ], [
                'required' => ':attribute wajib diisi!',
                'email' => ':attribute formatnya salah!',
                'url' => 'Format link :attribute tidak valid (harus pakai http/https)!',
                'numeric' => ':attribute harus berupa angka!',
            ], [
                'app_name' => 'Nama Aplikasi',
                'customer_care_phone' => 'Nomor CS',
                'operating_days_info' => 'Hari Operasional',
                'tiktok_url' => 'Link TikTok',
                'youtube_url' => 'Link YouTube',
                'facebook_url' => 'Link Facebook',
                'instagram_url' => 'Link Instagram',
                'x_url' => 'Link X (Twitter)',
                'longitude' => 'Garis Bujur (Longitude)',
                'latitude' => 'Garis Lintang (Latitude)',
            ]);

            if ($validator->fails()) {
                $response = $this->responseData->create(
                    'Validasi Gagal',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422,
                    isJson: false
                );

                return redirect()->back()->withInput()->with(compact('response'));
            }

            // Trik untuk handle checkbox boolean di Laravel
            $data = $request->all();
            $data['is_ordering_active'] = $request->has('is_ordering_active') ? true : false;

            $this->contactService->editHandler($data);

            $response = $this->responseData->create('Pengaturan berhasil diperbarui', isJson: false);

            return redirect()->back()->with(compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create('Terjadi kesalahan server', status: 'error', status_code: 500, isJson: false);

            return redirect()->back()->withInput()->with(compact('response'));
        }
    }
}
