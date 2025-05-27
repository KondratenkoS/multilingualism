<?php

use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', MenuController::class)->name('main.index');

Route::get('lang/{locale}', function ($locale) {
    if (!array_key_exists($locale, config('app.locales'))) {
        abort(404);
    }

    session(['locale' => $locale]);
    app()->setLocale($locale);

    return back();
})->name('lang.switch');


