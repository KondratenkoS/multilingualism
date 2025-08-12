<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Pages extends Model
{
    use HasTranslations, AsSource, Attachable;
    protected $table = 'pages';
    protected $fillable = ['slug', 'meta_title', 'meta_description', 'meta_keywords', 'title', 'body'];
    public array $translatable = ['slug', 'meta_title', 'meta_description', 'meta_keywords', 'title', 'body'];
}
