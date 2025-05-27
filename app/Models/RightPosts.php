<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class RightPosts extends Model
{
    use HasTranslations, AsSource;

    protected $table = 'right_posts';
    protected $fillable = ['title', 'body'];
    public $translatable = ['title', 'body',];

}
