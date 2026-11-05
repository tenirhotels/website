<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Spatie\Translatable\HasTranslations;

class Infrastructure extends Model
{
    use HasFactory, HasTranslations, Attachable;

    protected $fillable = ['title', 'content', 'position', 'image'];

    public $translatable = ['title', 'content'];

    public function getImageUrlAttribute()
    {
        return $this->attachment->first() ? $this->attachment->first()->url() : null;
    }
}
