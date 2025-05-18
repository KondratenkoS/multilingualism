<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations, AsSource, Filterable, Attachable;

    protected $table = 'posts';
    protected $fillable = ['title', 'slug', 'body'];
    public $translatable = ['title', 'body'];

    public function post_contents(): HasMany
    {
        return $this->hasMany(PostContent::class);
    }

    public function getTitleEnAttribute(): ?string
    {
        return $this->getTranslation('title', 'en');
    }

    public function setTitleEnAttribute(string $value): void
    {
        $this->setTranslation('title', 'en', $value);
    }

    public function getTitleHeAttribute(): ?string
    {
        return $this->getTranslation('title', 'he');
    }

    public function setTitleHeAttribute(string $value): void
    {
        $this->setTranslation('title', 'he', $value);
    }

    public function getBodyEnAttribute(): ?string
    {
        return $this->getTranslation('body', 'en');
    }

    public function setBodyEnAttribute(string $value): void
    {
        $this->setTranslation('body', 'en', $value);
    }

    public function getBodyHeAttribute(): ?string
    {
        return $this->getTranslation('body', 'he');
    }

    public function setBodyHeAttribute(string $value): void
    {
        $this->setTranslation('body', 'he', $value);
    }
}
