<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Translation extends Model
{
    use HasTranslations;

    protected $fillable = ['slug', 'value'];

    public $translatable = ['value'];

    protected $casts = ['value' => 'array'];
}

