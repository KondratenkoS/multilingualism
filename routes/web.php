<?php

use App\Http\Controllers\AboutMeController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('lang/{locale}', function ($locale) {
    if (!array_key_exists($locale, config('app.locales'))) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);

    return back();
})->name('lang.switch');


Route::get('/', IndexController::class)->name('main.index');
Route::get('/about-me', AboutMeController::class)->name('about-me');
