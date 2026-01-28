<?php

use Illuminate\Support\Facades\Route;

Route::get('/hola', function () {
    return view('welcome');
})->name("que");
