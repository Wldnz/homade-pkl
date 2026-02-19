<?php

namespace App\Http\Controllers;

use App\ResponseData;
use App\Service\AchievementService;
use App\Service\CategoryService;
use App\Service\ContactService;
use App\Service\MenuService;
use App\Service\PackageService;
use App\Service\PartnerService;
use Illuminate\Http\Request;
use Response;


class UserController extends Controller
{
    private $packageService;
    private $categoryService;
    private $menuService;
    private ContactService $contactService;
    private PartnerService $partnerService;
    private AchievementService $achievementService;

    private ResponseData $responseData;

    public function __construct()
    {
        $this->packageService = new PackageService();
        $this->categoryService = new CategoryService();
        $this->menuService = new MenuService();
        $this->contactService = new ContactService();
        $this->achievementService = new AchievementService();
        $this->partnerService = new PartnerService();
        $this->responseData = new ResponseData();
    }

    public function index()
    {
        $response = $this->responseData->create(
            'Successfully Getting Data',
            [
                'packages' => $this->packageService->all(),
                'categories' => $this->categoryService->getSelectedCategoriesLabel(),
                // top populer menu / menu hari ini
                'footer' => $this->contactService->information(),
            ],
            isJson: false
        );

        return view('home', $response);
    }

    public function menus(Request $request)
    {
        // filter (?)
        $searchName = $request->query('search_name', '');

        $response = $this->responseData->create(
            'Successfully Getting Data',
            [
                'menus' => $this->menuService->withThemeAndCategory(search: $searchName),
                'footer' => $this->contactService->information()
            ]
        );

        return view('menus', $response);
    }

    public function detailMenu(string $id)
    {
        $menu = $this->menuService->searchByID($id);
        $categories = [];


        if ($menu) {
            foreach ($menu->menu_categories as $menuCategory) {
                if ($menuCategory->categories) {
                    array_push($categories, $menuCategory->categories->id);
                }
            }
        }

        $response = $this->responseData->create(
            'Succesfully Getting Data',
            [
                'menu' => $this->menuService->searchByID($id),
                'menu_relevant' => $this->menuService->getByRelevantCategoriesAndTheme(
                    $categories,
                    $menu->theme->name,
                    $menu->id
                ),
                'footer' => $this->contactService->information()
            ]
        );

        return view('detail-menu', $response);
    }

    public function schedules(Request $request)
    {
        $week = $request->query('week', 1);
        $response = $this->responseData->create(
            'Succesfully Getting Data',
            [
                'weekly' => $this->menuService->getWeeklyMenus($week),
                'footer' => $this->contactService->information()
            ]
        );
        return view('schedule', $response);
    }

    public function profile()
    {
        $response = $this->responseData->create(
            'Succesfully Getting Data!',
            [
                'achievements' => $this->achievementService->all(),
                'partners' => $this->partnerService->all(),
                'footer' => $this->contactService->information(),
            ]
        );
        return view('profile', $response);
    }

    public function contact()
    {
        $response = $this->responseData->create(
            'Successfully Getting Data!',
            [
                'contact' => $this->contactService->information(),
            ],
        );
        return view('contact', $response);
    }

    public function me(){
        $response = $this->responseData->create(
            'Succesfully Getting Data',
            [
                
            ]
        );
        return view('me', $response);
    }
}
