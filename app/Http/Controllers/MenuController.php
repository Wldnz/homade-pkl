<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailMenuResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\MenuScheduleResource;
use App\ResponseData;
use App\Service\ContactService;
use App\Service\MenuService;
use Exception;
use Illuminate\Http\Request;
use Log;

class MenuController extends Controller
{
    
    private MenuService $menuService;

    private ResponseData $responseData;

    private ContactService $contactService;

    public function __construct() {
        $this->menuService = new MenuService();
        $this->responseData = new ResponseData();
        $this->contactService = new ContactService();
    }

     public function all(Request $request)
    {
        try {
            
            $search = $request->query('search', '');
            $page = (int) $request->query('page', 1);
            $limit = (int) $request->query('limit', 5);

            $menus = $this->menuService->all(
                $search,
                $page,
                $limit,
            );

            if ($menus->isEmpty()) {
                $response = $this->responseData->create(
                    "Tidak dapat menemukan menu",
                    status_code: 404,
                    status: "warning",
                    isJson:false
                );
                return view('menus', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Menu - Menu',
                [
                    'menus' => MenuResource::collection($menus),
                    'meta' => [
                        'search' => $search,
                        'page' => $page,
                        'limit' => $limit,
                    ]
                ],
                isJson:false
            );

            return view('menus', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah terjadi kesalahan',
                status_code: 500,
                status: 'error',
                isJson:false
            );
            return view('menus', compact('response'));
        }
    }

    public function detail(string $id){
        try{

            $menu = $this->menuService->searchByID($id);

            if(!$menu){
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu',
                    status_code:404,
                    status: 'warning',
                    isJson:false
                );
                return view('detail-menu', compact('response'));
            }

            $categories_id = $menu->menu_categories->map(function($category){
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

            $response =  $this->responseData->create(
                'Berhasil mendapatkan menu',
                $menu,
                isJson:false
            );

            return view('detail-menu', compact('response'));

        }catch(Exception $e){
            if(str_starts_with($e->getMessage(), 'No query results for model')){
                $response = $this->responseData->create(
                    'Tidak dapat menemukan menu',
                    status_code:404,
                    status: 'warning',
                    isJson:false
                );
                return view('detail-menu', compact('response'));
            }
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah terjadi kesalahan pada server',
                status: 'error',
                status_code : 500,
                isJson:false
            );
            return view('detail-menu', compact('response'));
        }
    }

    public function weekly(Request $request) {
        try{            
            $week = (int) $request->query('week', 1);

            $menus = $this->menuService->getWeeklyMenus($week);

            if($menus->isEmpty()){
                $response =  $this->responseData->create(
                    'Tidak dapat menemukan menu mingguan',
                    status: 'warning',
                    status_code: 404,
                    isJson:false
                );
                return view('schedule', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil mendapatkan menu mingguan',
                MenuScheduleResource::collection($menus),
                isJson:false
            );

            return view('schedule', compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan',
                status:'error',
                status_code:500,
                isJson:false
            );
            return view('schedule', compact('response'));
        }
    }

}
