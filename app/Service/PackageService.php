<?php
namespace App\Service;
use App\Models\Package;

class PackageService{

    public function all(){
        return Package::all();
    }



}