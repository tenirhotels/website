<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Spatie\Translatable\HasTranslations;

class Variable extends Model
{
    use Attachable;

    protected $casts = [
        'data' => 'array'
    ];

    protected $fillable = ['slug', 'data'];

    protected $appends = ['images', 'files'];

    public function getImagesAttribute()
    {
        return $this->attachment->map(function (Attachment $attachment) {
            return $attachment->id;
        });
    }

    public function getFilesAttribute()
    {
        return $this->attachment->map(function (Attachment $attachment) {
            return $attachment->id;
        });
    }
}
