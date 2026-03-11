<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\ThemeResource;
use App\ResponseData;
use App\Service\ThemeService;
use Exception;
use Illuminate\Http\Request;
use Log;

class ThemeController extends Controller
{
    private ResponseData $responseData;
    private ThemeService $themeService;

    public function __construct()
    {
        $this->responseData = new ResponseData;
        $this->themeService = new ThemeService();
    }

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $limit = (int) $request->query('limit', 3);

            $themes = $this->themeService->all($search, $limit);

            if ($themes->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan tema',
                    status: 'warning',
                    status_code: 404,
                );
            }

           return $this->responseData->create(
                'Berhasil mendapatkan Tema',
                [
                    'pagination' => new PaginationResource($themes),
                    'themes' => ThemeResource::collection($themes),
                ],
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
