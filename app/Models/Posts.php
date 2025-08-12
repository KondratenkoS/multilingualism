<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Posts extends Model
{
    use HasTranslations, AsSource, Attachable;
    protected $table = 'posts';
    protected $fillable = ['position', 'link', 'type', 'title', 'body'];
    public array $translatable = ['title', 'body'];
}
