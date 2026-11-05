<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Spatie\Translatable\HasTranslations;

class HotelPrivilege extends Model
{
    use HasFactory, HasTranslations, Attachable;

    protected $fillable = ['title', 'position'];

    public $translatable = ['title'];

    protected $appends = ['image'];

    public function getImageAttribute()
    {
        return $this->attachment->first()->id ?? null;
    }

    public function getImageUrlAttribute()
    {
        return $this->attachment->first() ? $this->attachment->first()->url() : null;
    }
}
