<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuController extends Controller
{
      public function index(){
        return view('admin.menu.index');
    }

     public function detail(string $id){
        return view('admin.menu.detail');
    }

     public function store(){
        return view('admin.menu.store');
    }
}
