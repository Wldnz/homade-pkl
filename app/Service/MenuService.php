<?php
namespace App\Service;
use App\Models\Menu;

class MenuService{

    public function all(){
        return Menu::all();
    }

    public function getWeeklyPopuler(){
        // ini dapet bisa dikategorikan populer dari mana?
    }

}