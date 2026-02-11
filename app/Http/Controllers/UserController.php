<?php

namespace App\Http\Controllers;

use App\Service\AchievementService;
use App\Service\CategoryService;
use App\Service\ContactService;
use App\Service\MenuService;
use App\Service\PackageService;
use App\Service\PartnerService;
use Illuminate\Http\Request;


class UserController extends Controller
{
    private $packageService;
    private $categoryService;
    private $menuService;
    private ContactService $contactService;
    private PartnerService $partnerService;
    private AchievementService $achievementService;

    public function __construct()
    {
        $this->packageService = new PackageService();
        $this->categoryService = new CategoryService();
        $this->menuService = new MenuService();
        $this->contactService = new ContactService();
        $this->achievementService = new AchievementService();
        $this->partnerService = new PartnerService();
    }

    public function index()
    {
        return view('home', [
            'packages' => $this->packageService->all(),
            'categories' => $this->categoryService->getSelectedCategoriesLabel(),
            // top populer menu / menu hari ini
            'footer' => $this->contactService->information(),
        ]);
    }

    public function menus(Request $request)
    {
        // filter (?)
        $searchName = $request->query('search_name', '');

        return view('menus', [
            'menus' => $this->menuService->withThemeAndCategory(
                search: $searchName
            ),
            'footer' => $this->contactService->information()
        ]);
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

        return view('detail-menu', [
            'menu' => $this->menuService->searchByID($id),
            'menu_relevant' => $this->menuService->getByRelevantCategoriesAndTheme(
                $categories,
                $menu->theme->name,
                $menu->id
            ),
            'footer' => $this->contactService->information()
        ]);
    }

    public function schedules(Request $request)
    {
        $week = $request->query('week', 1);
        return view('schedule', [
            'weekly' => $this->menuService->getWeeklyMenus($week),
            'footer' => $this->contactService->information()
        ]);
    }

    public function profile()
    {
        return view('profile', [
            'achievements' => $this->achievementService->all(),
            'partners' => $this->partnerService->all(),
            'footer' => $this->contactService->information(),
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'contact' => $this->contactService->information(),
        ]);
    }

    public function signup()
    {
        return view('signup');
    }

    public function signin()
    {
        return view('signin');
    }
    public function test()
    {
        return response()->json([
            'menus' => $this->menuService->withThemeAndCategory(),
        ]);
    }

}
