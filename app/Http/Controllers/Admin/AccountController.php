<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(){
        return view('admin.account.index');
    }

     public function detail(string $id){
        return view('admin.account.detail');
    }

     public function store(){
        return view('admin.account.store');
    }
}
