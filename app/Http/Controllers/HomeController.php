<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\ResponseData;
use App\Service\CategoryService;
use App\Service\MenuService;
use App\Service\PackageService;
use Exception;
use Log;


class HomeController extends Controller
{
    private ResponseData $responseData;
    private MenuService $menuService;
    private PackageService $packageService;
    private CategoryService $categoryService;

    public function __construct() {
        $this->responseData = new ResponseData();
        $this->menuService = new MenuService();
        $this->packageService = new PackageService();
        $this->categoryService = new CategoryService();
    }

    public function home(){
        // packages, menu_category (selected or idk), weekly populer menu (ini jadi today menu aja lah ya!)

        try{

            $categories = $this->categoryService->getSelectedCategoriesLabel();

            // weekly sementara jadi today menu aja ya!

            $packages = $this->packageService->all();

            $response = $this->responseData->create(
                'Berhasil mendapatkan data!',
                [
                    'categories' => $categories,
                    'packages' => PackageResource::collection($packages),
                ]
            );

            return view('home', compact('response'));

        }catch(Exception $e){
            Log::error($e->getMessage());
            $this->responseData->create(
                'Telah Terjadi Kesalahan Pada Server',
                status: 'error',
                status_code:500,
                isJson:false
            );
            return view('home', compact('response'));
        }

    }
}
