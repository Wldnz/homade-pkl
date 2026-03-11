<?php
namespace App\Service;
use App\Models\Package;

class PackageService{

    public function all(
        string|null $search=null,
        $limit= 3,
        bool $is_has_limit = false,
    ){
        $packages = Package::when($search, function($query, $search){
            $search = strtolower($search);
            return $query->whereRAW('LOWER(name) LIKE ?', "%$search%");
        });
        if($is_has_limit){
            return $packages->paginate($limit);
        }
        return $packages->get();
    }



}