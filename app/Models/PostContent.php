<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class PostContent extends Model
{
    use HasTranslations, AsSource;

    protected $table = 'post_contents';
    protected $fillable = ['title', 'slug', 'body', 'meta_title', 'meta_description', 'meta_keywords'];
    public $translatable = ['title', 'body', 'meta_title', 'meta_description', 'meta_keywords'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(LeftPost::class);
    }
}
