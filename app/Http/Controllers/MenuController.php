<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailMenuResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\MenuScheduleResource;
use App\Http\Resources\PaginationResource;
use App\Http\Resources\SelectMenuResource;
use App\ResponseData;
use App\Service\MenuService;
use App\TransactionCategory;
use App\Utils\ConvertDateSafely;
use App\Utils\TransactionHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Log;

class MenuController extends Controller
{

    private MenuService $menuService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->responseData = new ResponseData();
    }

    public function all(Request $request)
    {
        try {

            $search = $request->query('search', '');
            $theme = $request->query('theme');
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
                $response = $this->responseData->create(
                    "Tidak dapat menemukan menu",
                    status_code: 404,
                    status: "warning",
                    isJson: false
                );
                return view('menus', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Menu - Menu',
                [
                    'pagination' => (new PaginationResource($menus))->toArray($request),
                    'items' => MenuResource::collection($menus)->toArray($request),
                ],
                isJson: false
            );

            return view('menus', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah terjadi kesalahan',
                status_code: 500,
                status: 'error',
                isJson: false
            );
            return view('menus', compact('response'));
        }
    }

    public function detail(Request $request,string $id)
    {
        try {

            $menu = $this->menuService->searchByID($id);

            if (!$menu) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu',
                    status_code: 404,
                    status: 'warning',
                    isJson: false
                );
                return view('detail-menu', compact('response'));
            }

            $categories_id = $menu->menu_categories->map(function ($category) {
                return $category->categories->id;
            });

            $relevants = $this->menuService->getByRelevantCategoriesAndTheme(
                $categories_id,
                $id
            );

            $menu = new DetailMenuResource([
                'menu' => $menu,
                'relevants' => $relevants
            ]);

            $response = $this->responseData->create(
                'Berhasil mendapatkan menu',
                $menu->toArray($request),
                isJson: false
            );

            return view('detail-menu', compact('response'));

        } catch (Exception $e) {
            if (str_starts_with($e->getMessage(), 'No query results for model')) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu',
                    status_code: 404,
                    status: 'warning',
                    isJson: false
                );
                return view('detail-menu', compact('response'));
            }
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah terjadi kesalahan pada server',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('detail-menu', compact('response'));
        }
    }

    public function weekly(Request $request)
    {
        try {
            $week = (int) $request->query('week', 1);

            $menus = $this->menuService->getWeeklyMenus($week);

            if ($menus->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu mingguan',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );
                return view('schedule', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan menu mingguan',
                MenuScheduleResource::collection($menus)->toArray($request),
                isJson: false
            );

            return view('schedule', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan',
                status: 'error',
                status_code: 500,
                isJson: false
            );
            return view('schedule', compact('response'));
        }
    }

    public function selectWeekly(Request $request)
    {
        try {

            $date_at = $request->query('date_at', now()->addDays(1));
            // ini perlu di perbaiki nih, kalo error berabe
            $date_at = (new ConvertDateSafely)->convert($date_at, now()->addDays(1));

            $menus = $this->menuService->getBySingleDate($date_at);

            // $date_at

            if ($menus->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu mingguan pada tanggal ini',
                    status: 'warning',
                    status_code: 404,
                    isJson: false
                );
                return view('order.select-menu-weekly', compact('response'));
            }

            $transactionHelper = new TransactionHelper();
            if (!$transactionHelper->canOrderDeliveryWeeklyMenu($date_at)) {
               $response=  $this->responseData->create(
                    'Menu mingguan hanya dapat di pesan pada h-1 dan paling lambar di jam 3 sore',
                    status: 'warning',
                    status_code: 400,
                    isJson: false
                );
                return view('order.select-menu-weekly', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Menemukan Menu Mingguan Pada Tanggal Ini',
                [
                    'date' => $date_at->format('d-m-Y'),
                    'category' => TransactionCategory::ORDER,
                    'items' => SelectMenuResource::collection($menus)->toArray($request)
                ],
                isJson: false,
            );

            return view('order.select-menu-weekly', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return view('order.select-menu-weekly', compact('response'));
        }
    }

    public function select(Request $request)
    {
        try {

            $search = $request->query('search');
            $theme = $request->query('theme');
            $category = $request->query('category'); //category menu
            $limit = $request->query('limit', 10); 

            $menus = $this->menuService->menuNonWeekly(
                $search,
                $theme,
                $category,
                $limit,
            );

            if ($menus->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak Dapat Menemukan Menu Menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('order.select-menu', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil menemukan menu',
                [
                    'category' => TransactionCategory::PRE_ORDER,
                    'pagination' => (new PaginationResource(resource: $menus))->toArray($request),
                    'items' => SelectMenuResource::collection($menus)->toArray($request),
                ],
                isJson: false,
            );

            return view('order.select-menu', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
            );
            return view('order.select-menu', compact('response'));
        }
    }

}