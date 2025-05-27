<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\IntroPost;
use App\Models\Menu;
use App\Models\LeftPost;
use App\Models\RightPosts;

class MenuController extends Controller
{
    public function __invoke()
    {
        app()->setLocale(session('locale', config('app.locale')));

        $menus = Menu::all();
        $contacts = Contact::first(); // Тут вже будуть переклади потрібною мовою
        $logo = $contacts?->attachments()->first();
        $rightPosts = RightPosts::all();
        $leftPosts = LeftPost::all();
        $introPosts = IntroPost::all();
        $languages = config('app.locales');


        return view('main', compact('menus', 'logo', 'introPosts', 'rightPosts', 'leftPosts', 'contacts', 'languages'));
    }
}
