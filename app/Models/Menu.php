<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasTranslations, AsSource, Filterable, Attachable;

    protected $table = 'menus';
    protected $fillable = ['title', 'slug'];
    public $translatable = ['title'];

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
}
