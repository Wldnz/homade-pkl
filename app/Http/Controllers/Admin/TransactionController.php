<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
    {
        return view('admin.order.index');
    }

    public function detail(string $id)
    {
        return view('admin.order.detail');
    }

    public function store()
    {
        return view('admin.order.store');
    }
}
