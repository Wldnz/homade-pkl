<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/menu', function () {
    return view('menus');
});

Route::get('/menu/:id', function () {
    return view('detail-menu');
});
Route::get('/schedule', function () {
    return view('schedule');
});
