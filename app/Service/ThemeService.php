<?php

namespace App\Service;

use App\Models\Theme;

class ThemeService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(
        $limit = 5
    ){
        return Theme::paginate($limit);
    }

    public function detail(string $id){
        return Theme::where('id', $id)->first();
    }

    public function save(array $data){
        return Theme::create([
            'name' => $data['name'],
            'description'=> $data['description'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function edit(Theme $theme, array $data){
        $theme->name = $data['name'];
        $theme->description = $data['description'];
        $theme->save();
        return $theme;
    }

    public function delete(){}

}
