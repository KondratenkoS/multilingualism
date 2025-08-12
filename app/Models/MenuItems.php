<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class MenuItems extends Model
{
    use HasTranslations, AsSource;
    protected $table = 'menu_items';
    protected $fillable = ['title', 'link'];
    public array $translatable = ['title'];
}
