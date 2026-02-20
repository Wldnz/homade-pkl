<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuScheduleResource;
use App\ResponseData;
use App\Service\MenuService;
use Exception;
use Illuminate\Http\Request;
use Log;
use Validator;

class MenuController extends Controller
{

    private ResponseData $responseData;
    private MenuService $menuService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->menuService = new MenuService();
    }

    public function menu(Request $request)
    {

        $date_at = $request->query('date_at', now());

        if($date_at){
            return $this->getByDate($request);
        }

        return $this->responseData->create(
            'Berhasil Mendapatkan Menu Berdasarkan Tanggal Sekarang',
            $this->menuService->getByDate($date_at)
        );

    }

    private function getByDate(Request $request)
    {
        try {
            $date_at = $request->query('date_at', now());

            if (empty($date_at)) {
                return $this->responseData->create(
                    'Pastikan tanggal yang dimasukkan tidak kosong',
                    status: 'warning',
                    status_code: 422
                );
            }

            $menu = $this->menuService->getByDate($date_at);

            if (!$menu) {
                return $this->responseData->create(
                    'Tidak dapat menemukan menu berdasarkan tanggal',
                    status: 'warning',
                    status_code: 404
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan menu berdasarkan tanggal',
                MenuScheduleResource::collection($menu)
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah terjadi kesalahan',
                status:'error',
                status_code:500,
            );
        }
    }

}