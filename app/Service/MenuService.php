<?php
namespace App\Service;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuSchedule;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\search;

class MenuService{

    public function all(){
        return Menu::all();
    }

    public function getWeeklyPopuler(){
        // ini dapet bisa dikategorikan populer dari mana?
    }

    public function searchByID(string | int $id){
        return Menu::findOrFail($id)
        ->with([
            'menu_categories',
            'theme',
            'prices',
        ])->select([
            'id', 'id_theme' , 'name', 'description', 'vegetable', 'side_dish', 'chili_sauce', 'image_url', 'is_active'
        ])->first();
    }

    public function withThemeAndCategory(
        bool $isActive = true,
        string $search = '',
    ){
        $menus = Menu::with([
            'menu_categories',
            'theme'
        ])->select([
            'id', 'id_theme' , 'name', 'description', 'vegetable', 'side_dish', 'chili_sauce', 'image_url'
        ])
        ->where('is_active', $isActive);

        if(!empty($search)){
            $searchParams = '%' . strtolower($search) .'%';
            $menus = $menus->whereRaw('LOWER(name) LIKE ?', [$searchParams] );
        }

        return $menus->get();
    }

    public function getByRelevantCategoriesAndTheme(
        array $menuCategories,
        string $theme_id,
        string $menu_id
    ){
        // limit tiga aja kali ya
        // berdasarkan tema dan kategori
        $menus = Menu::whereHas('menu_categories', fn ($query)=> $query->whereIn('id_category', $menuCategories))
        ->with([
            'menu_categories' => fn ($query)=> $query->whereIn('id_category', $menuCategories)->limit(3),
            'theme'
        ])
        ->whereNot('id', $menu_id)->get();
        return $menus;
    }

    public function getWeeklyMenus(
        int $week = 1
    ){
        $startTime = str_split(now()->addDays(7 * ($week - 1)), 10)[0];
        $endTime = str_split(now()->addDays(7 * $week), 10)[0];
        // custom weeklynya, dihitung dari hari ini + (7 * index)
        $menus = MenuSchedule::with([
            'menu'
        ])->whereBetween('date_at', [$startTime, $endTime])
        ->get();
        return $menus;
    }

    public function getByDate(string $date){
        return MenuSchedule::where('date_at', 'Like', "%$date%")
        ->with('menu')
        ->get();
    }

    public function usingFilter(
        string | null $date
    ){
        // return 
    }

}