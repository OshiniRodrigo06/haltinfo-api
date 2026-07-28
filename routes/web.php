<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Fix for "Route [login] not defined" error
Route::get('/login', function () {
    return redirect('/staff/login');
})->name('login');
