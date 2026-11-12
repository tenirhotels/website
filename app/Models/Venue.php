<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venue extends Model
{
    use \App\Models\Concerns\ForcesEditorHotel;
    use HasSeo, HasTranslations;

    protected $guarded = ['id'];

    protected array $translatable = ['name', 'summary', 'description', 'hours', 'break_hours', 'breakfast_hours'];

    protected string $titleAttribute = 'name';

    protected $casts = ['gallery' => 'array'];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
