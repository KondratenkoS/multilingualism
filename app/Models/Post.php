<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations, AsSource, Filterable;

    protected $table = 'posts';
    protected $fillable = ['title', 'slug', 'body', 'meta_title', 'meta_description', 'meta_keywords'];
    public $translatable = ['title', 'body', 'meta_title', 'meta_description', 'meta_keywords'];

    public function post_contents(): HasMany
    {
        return $this->hasMany(PostContent::class);
    }
}
