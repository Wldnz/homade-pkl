<?php
namespace App\Service;
use App\Models\Category;
use App\Models\MenuCategory;

class CategoryService{

    public function all(){
        return Category::all();
    }

    public function getSelectedCategoriesLabel(){

        // sementara pake ini dlu wkwkkw
        // postgress case sensitive wkkw
        $selectedCategories = [
            [ "Nasi" ], [ "Ayam" ], [ "Ikan", "Seafood" ], [ "Sapi", "Kambing" ]
        ];

        $selected = [];
        foreach($selectedCategories as $sc){
            $key = '';
            foreach($sc as $category){
               $key .= $key? ", $category" : $category;
            }
            array_push($selected, [
                'label' => $key,
                'total' => MenuCategory::whereRaw("id_category IN (select id from categories c  where name in (?) )", [$key])->count("id")
            ]);
        }
        return $selected;
    }

}