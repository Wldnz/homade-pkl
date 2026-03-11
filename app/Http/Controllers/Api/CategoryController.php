<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\CategoryService;
use Exception;
use Illuminate\Http\Request;
use Log;

class CategoryController extends Controller
{
    private ResponseData $responseData;

    private CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService;
        $this->responseData = new ResponseData;
    }

    public function index(Request $request)
    {
        try {

            $search = $request->query('search');
            $limit = (int) $request->query('limit', 3);

            $categories = $this->categoryService->all($search, $limit);

            if ($categories->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan category',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan category',
                [
                    'pagination' => new PaginationResource($categories),
                    'categories' => CategoryResource::collection($categories),
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
