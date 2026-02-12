<?php

namespace App\Http\Controllers;

use App\Service\AchievementService;
use App\Service\ContactService;
use App\Service\MenuService;
use App\Service\PartnerService;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{

    private MenuService $menuService;
    private AchievementService $achievementService;
    private PartnerService $partnerService;

    private ContactService $contactService;

    public function __construct() {
        $this->menuService = new MenuService();
        $this->achievementService = new AchievementService();
        $this->partnerService = new PartnerService();
        $this->contactService = new ContactService();
    }

   
    public function userMenus(Request $request){

        $search = $request->query('search_name', '');

        return response()->json([
            'menus' => $this->menuService->withThemeAndCategory(
                search : $search
            ),
        ]);
    }

    public function userDetailMenu(string $id){
        $menu = $this->menuService->searchByID($id);
        $categories = [];
        
        if($menu){
            foreach($menu->menu_categories as $menuCategory){
                if($menuCategory->categories){
                    array_push($categories, $menuCategory->categories->id);
                }
            }
        }

        return response()->json([
            'menu' => $menu,
            'menu_relevan' => $this->menuService->getByRelevantCategoriesAndTheme(
                $categories,
                $menu->theme->name,
                $menu->id
            ),
        ]);
    }

    public function userWeeklyMenus(Request $request){
        // can negative or positive (please do not provide 0 ya)

        $indexWeekly = $request->query('week', 1);

        if($indexWeekly == 0){
            return response()->json([
                'message' => 'week value cannot be 0',
            ],
            400
        );
        }

        return response()->json([
            'weekly' => $this->menuService->getWeeklyMenus($indexWeekly),
        ]);
    }

    public function achievements(){
        return response()->json([
            'achievements' => $this->achievementService->all([ 'name', 'description', 'date_at' ] ),
            'partners' => $this->partnerService->all( [ 'name', 'name', 'image_url' ] )
        ]);
    }

    public function contact(){
        return response()->json([
            'contact' => $this->contactService->information(),
        ]);
    }

    public function additional(){
        return response()->json([
            'footer' => $this->contactService->information(),
        ]);
    }


}
