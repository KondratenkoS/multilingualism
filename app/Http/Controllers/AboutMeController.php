<?php

namespace App\Http\Controllers;

use App\Models\MenuItems;
use App\Models\Pages;
use App\Models\Settings;

class AboutMeController extends Controller
{
    public function __invoke()
    {
        app()->setLocale(session('locale', config('app.locale')));
        $languages = config('app.locales');

        $settings = Settings::first();
        $menus = MenuItems::all();
        $page = Pages::find(1);
        $logo = $settings?->attachments()->first();

        return view('aboutMe', compact( 'settings', 'page','menus','languages', 'logo'));
    }
}
