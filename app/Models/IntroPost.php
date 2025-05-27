<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class IntroPost extends Model
{
    use HasTranslations, AsSource;
    protected $table = 'intro_posts';
    protected $fillable = ['intro_title', 'intro_post', 'left_intro_title', 'left_intro_post', 'right_intro_title', 'right_intro_post'];
    public $translatable = ['intro_title', 'intro_post', 'left_intro_title', 'left_intro_post', 'right_intro_title', 'right_intro_post'];
}
