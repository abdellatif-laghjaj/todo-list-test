<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Vue.js SPA route
Route::get('/app/{any?}', function () {
    return view('app');
})->where('any', '.*');
