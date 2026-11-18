<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/** Слоган бренда: «Back to Nature» (заменяет «Ближе к небу», если слоган не меняли вручную). */
return new class extends Migration
{
    public function up(): void
    {
        $brand = Setting::get('brand', []);
        $old = $brand['tagline'] ?? [];
        $isDefault = ! array_filter($old) || ($old['ru'] ?? null) === 'Ближе к небу';

        if ($isDefault) {
            $brand['tagline'] = ['ru' => 'Назад к природе', 'en' => 'Back to Nature', 'kk' => 'Табиғатқа оралу', 'zh' => '回归自然'];
            Setting::put('brand', $brand);
        }
    }

    public function down(): void
    {
        //
    }
};
