<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Settings extends Model
{
    use HasTranslations, AsSource, Attachable;
    protected $table = 'settings';
    protected $fillable = ['meta_title', 'meta_description', 'meta_keywords', 'gtm_head', 'gtm_body',
                            'menu_items', 'email', 'phone_for_call', 'phobe_for_chat', 'copyright'];
    public array $translatable = ['meta_title', 'meta_description', 'meta_keywords', 'menu_items', 'copyright'];
}
