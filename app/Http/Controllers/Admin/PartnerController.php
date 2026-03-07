<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(){
        return view('admin.partner.index');
    }

     public function detail(string $id){
        return view('admin.partner.detail');
    }

     public function store(){
        return view('admin.partner.store');
    }
}
