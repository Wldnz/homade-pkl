<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailMenuResource;
use App\Http\Resources\MenuByDateResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\MenuScheduleResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\SelectMenuResource;
use App\ResponseData;
use App\Service\MenuService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
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
        try {

            $search = $request->query('search');
            $theme = $request->query('theme', '');
            $category = $request->query('category');
            $page = (int) $request->query('page', 1);
            $limit = (int) $request->query('limit', 5);

            $menus = $this->menuService->all(
                $search,
                $theme,
                $category,
                $limit,
            );

            if ($menus->isEmpty()) {
                return $this->responseData->create(
                    "Tidak dapat menemukan menu",
                    status_code: 404,
                    status: "warning"
                );
            }

            return $this->responseData->create(
                'Berhasil Mendapatkan Menu - Menu',
                [
                    'pagination' => new PaginationResource($menus),
                    'items' => MenuResource::collection($menus)
                ]
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah terjadi kesalahan',
                status_code: 500,
                status: 'error'
            );
        }
    }

    public function detail(string $id)
    {
        // menu -> tema -> package -> relevantMenu
        try {
            $menu = $this->menuService->searchByID($id);
            if (!$menu) {
                return $this->responseData->create(
                    'Tidak dapat menemukan menu',
                    status_code: 404,
                    status: 'warning'
                );
            }

            $categories_id = $menu->menu_categories->map(function ($category) {
                return $category->categories->id;
            });



            $relevants = $this->menuService->getByRelevantCategoriesAndTheme(
                $categories_id,
                // $menu->theme->id,
                $id
            );

            $menu = new DetailMenuResource([
                'menu' => $menu,
                'relevants' => $relevants
            ]);

            return $this->responseData->create(
                'Berhasil mendapatkan menu',
                $menu,
            );

        } catch (Exception $e) {
            if (str_starts_with($e->getMessage(), 'No query results for model')) {
                return $this->responseData->create(
                    'Tidak dapat menemukan menu',
                    status_code: 404,
                    status: 'warning'
                );
            }
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah terjadi kesalahan pada server',
                status: 'error',
                status_code: 500
            );
        }
    }

    public function weekly(Request $request): array|JsonResponse
    {
        try {
            $week = (int) $request->query('week', 1);

            $menus = $this->menuService->getWeeklyMenus($week);

            if ($menus->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan menu mingguan',
                    status: 'warning',
                    status_code: 404,
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan menu mingguan',
                MenuScheduleResource::collection($menus)
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan',
                status: 'error',
                status_code: 500,
            );
        }
    }

    public function getByDate(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'date' => 'date|required'
            ],[
                'date' => 'pada request :attribute pastikan yang dimasukkan adalah sebuah tanggal',
                'required' => ':attribute dibutuhkan'
            ], [
                'date' => 'Tanggal Menu Tertentu'
            ]);

            if ($validator->fails()) {
                return $this->responseData->create(
                    'Pastikan tanggal yang dimasukkan tidak kosong',
                    errors: $validator->errors()->toArray(),
                    status: 'warning',
                    status_code: 422
                );
            }

            $date_at = Carbon::parse($request->date);

            $menus = $this->menuService->getByDate($date_at);

            if ($menus->isEmpty()) {
                return $this->responseData->create(
                    'Tidak dapat menemukan menu berdasarkan tanggal',
                    status: 'warning',
                    status_code: 404
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan menu berdasarkan tanggal',
                SelectMenuResource::collection($menus)
                // $menus
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah terjadi kesalahan',
                status: 'error',
                status_code: 500,
            );
        }
    }

    public function getPopulerWeeklyMenu(){
        try{

            $menus = $this->menuService->getWeeklyPopuler();

            if($menus->isEmpty()){
                return $this->responseData->create(
                    'Tidak Dapat Menemukan Menu Populer Mingguan',
                    status:'warning',
                    status_code: 404
                );
            }

            return $this->responseData->create(
                'Berhasil mendapatkan menu populer mingguan',
                MenuResource::collection($menus)
            );

        }catch(Exception $e){
            Log::error($e->getMessage());
            return $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status:'error',
                status_code: 500
            );
        }
    }


}