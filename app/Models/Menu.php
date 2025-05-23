<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasTranslations, AsSource;

    protected $table = 'menus';
    protected $fillable = ['title', 'slug'];
    public $translatable = ['title'];
}
