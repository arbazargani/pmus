<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});


Route::get('/menubar', function () {
    return view('home');
})->name('menubar');