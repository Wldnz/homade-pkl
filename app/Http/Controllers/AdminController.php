<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function signin(){
        return view('admin.signin');
    }

    public function dashboard(){
        return view('admin.dashboard');
    }

    public function menus(){
        return view('admin.menus');
    }

    public function schedules(){
        return view('admin.schedules');
    }

    public function orders(){
        return view('admin.orders');
    }

    public function detailOrder(string $id){
        return view('admin.detail-order');
    }
}
