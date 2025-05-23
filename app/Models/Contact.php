<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;
use Spatie\Translatable\HasTranslations;

class Contact extends Model
{
    use HasTranslations, AsSource, Attachable;

    protected $table = 'contacts';
    protected $fillable = ['copyright', 'email', 'phone_for_call', 'phone_for_chat', 'gtm_head', 'gtm_body'];
    public $translatable = ['copyright'];
}
