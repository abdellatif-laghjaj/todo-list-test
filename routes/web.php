<?php

use Illuminate\Support\Facades\Route;

// Redirect root to Vue app
Route::get('/', function () {
    return redirect('/app');
});

// Vue.js SPA route
Route::get('/app/{any?}', function () {
    return view('app');
})->where('any', '.*');
