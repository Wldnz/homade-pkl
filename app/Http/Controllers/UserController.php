<?php

namespace App\Http\Controllers;

use App\Service\CategoryService;
use App\Service\PackageService;


class UserController extends Controller
{
     private $packageService;
    private $categoryService;

    public function __construct() {
        $this->packageService = new PackageService();
        $this->categoryService = new CategoryService();
    }

    public function index(){
        return view('home', [ 
            'packages' => $this->packageService->all(),
            'categories' => $this->categoryService->getSelectedCategoriesLabel(),
         ]);
    }
}
