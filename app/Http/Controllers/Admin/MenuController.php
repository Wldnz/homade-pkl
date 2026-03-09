<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailMenuResource;
use App\Http\Resources\PaginationResource;
use App\ResponseData;
use App\Service\MenuService;
use Exception;
use Illuminate\Http\Request;
use Log;

class MenuController extends Controller
{

    private ResponseData $responseData;
    private MenuService $menuService;

    public function __construct()
    {
        $this->responseData = new ResponseData();
        $this->menuService = new MenuService();
    }

    public function index(Request $request)
    {
        // buthu revisi lai si ini
        try {

            $search = $request->query('search');
            $theme = $request->query('theme');
            $category = $request->query('category');
            $limit = $request->query('limit', 8);
            $isactive = (bool) $request->query('is_active', true);

            $menus = $this->menuService->all(
                $search,
                $theme,
                $category,
                $limit,
                $isactive
            );

            if ($menus->isEmpty()) {
                $response = $this->responseData->create(
                    'Tidak dapa menemukan menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.menu.index', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Menu',
                [
                    'pagination' => new PaginationResource($menus),
                    'menus' => $menus,
                ],
                isJson: false
            );

            return view('admin.menu.index', compact('response'));

        } catch (Exception $e) {
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return view('admin.menu.index', compact('response'));
        }
    }

    public function detail(Request $request,string $id)
    {
        try {

            $menu = $this->menuService->searchByID($id);

            if (!$menu) {
                $response = $this->responseData->create(
                    'Tidak dapa menemukan menu',
                    status: 'warning',
                    status_code: 404,
                    isJson: false,
                );
                return view('admin.menu.detail', compact('response'));
            }

            $response = $this->responseData->create(
                'Berhasil Mendapatkan Menu',
                (new DetailMenuResource([
                    'menu' => $menu,
                    'relevants' => [],
                ]))->toArray($request),
                isJson: false
            );

            return view('admin.menu.detail', compact('response'));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return view('admin.menu.detail', compact('response'));
        }
    }

    public function store()
    {
        return view('admin.menu.store');
    }

    public function storeHandler(Request $request, string $id){
        try{

        }catch(Exception $e){
              $response = $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code: 500,
                isJson: false,
            );
            return redirect()->back()->withInput()->with(compact('response'));
        }
    }

}
