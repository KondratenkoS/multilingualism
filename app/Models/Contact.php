<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Contact extends Model
{
    use HasTranslations, AsSource, Attachable, Filterable;

    protected $table = 'contacts';
    protected $fillable = ['copyright', 'email', 'phone_for_call', 'phone_for_chat'];
    public $translatable = ['copyright'];

    public function getCopyrightEnAttribute(): ?string
    {
        return $this->getTranslation('copyright', 'en');
    }

    public function setCopyrightEnAttribute(string $value): void
    {
        $this->setTranslation('copyright', 'en', $value);
    }

    public function getCopyrightHeAttribute(): ?string
    {
        return $this->getTranslation('copyright', 'he');
    }

    public function setCopyrightHeAttribute(string $value): void
    {
        $this->setTranslation('copyright', 'he', $value);
    }
}
