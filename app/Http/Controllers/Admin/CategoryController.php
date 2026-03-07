<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        return view('admin.category.index');
    }

     public function detail(string $id){
        return view('admin.category.detail');
    }

     public function store(){
        return view('admin.category.store');
    }
}
