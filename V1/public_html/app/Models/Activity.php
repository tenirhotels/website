<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Spatie\Translatable\HasTranslations;

class Activity extends Model
{
    use HasFactory, HasTranslations, Attachable;

    protected $fillable = ['title', 'content', 'images', 'position'];

    public $translatable = ['title', 'content'];

    protected $appends = ['image'];

    protected $casts = ['images' => 'array'];


    public function getImageAttribute()
    {
        return $this->attachment->first()->id ?? null;
    }

    public function getImageUrlAttribute()
    {
        return $this->attachment->first() ? $this->attachment->first()->url() : null;
    }

    public function getSlidersAttribute()
    {
        return Attachment::whereIn('id', $this->images)->get();
    }
}
