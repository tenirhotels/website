<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasFactory, HasTranslations, Attachable;

    protected $fillable = ['title', 'content', 'published_at', 'photo'];

    public $translatable = ['title', 'content'];

    protected $appends = ['image'];

    protected $dates = [
        'published_at'
    ];

    public function getImageAttribute()
    {
        return $this->attachment->first()->id ?? null;
    }

    public function getImageUrlAttribute()
    {
        return $this->attachment->first() ? $this->attachment->first()->url() : null;
    }

    public function getPublishedAtDefAttribute()
    {
	switch(app()->getLocale()){
		case "kk": 
			return $this->published_at->format('d').' '.
	            monthsName($this->published_at->format('m')).' '.
	            $this->published_at->format('Y');
		case "en":
			return monthsName($this->published_at->format('m')).' '.
                    ($this->published_at->format('d')).', '.
                    $this->published_at->format('Y');
		default:
                        return $this->published_at->format('d').' '.
                    monthsName($this->published_at->format('m')).' '.
                    $this->published_at->format('Y');


	}
    }
}
