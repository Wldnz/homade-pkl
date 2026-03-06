<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(){
        return view('admin.schedule.index');
    }

     public function detail(string $id){
        return view('admin.schedule.detail');
    }

     public function store(){
        return view('admin.schedule.store');
    }
}
