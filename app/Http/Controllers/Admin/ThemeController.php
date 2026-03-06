<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
      public function index(){
        return view('admin.theme.index');
    }

     public function detail(string $id){
        return view('admin.theme.detail');
    }

     public function store(){
        return view('admin.theme.store');
    }
}
