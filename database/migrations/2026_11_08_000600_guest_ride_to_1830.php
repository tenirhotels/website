<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/** Подъём для гостей отеля — с 18:00 до 18:30 (если в настройках осталось прежнее 19:00). */
return new class extends Migration
{
    public function up(): void
    {
        $cable = (array) Setting::get('cable', []);
        if (($cable['guest_to'] ?? null) === '19:00') {
            $cable['guest_to'] = '18:30';
            Setting::put('cable', $cable);
        }
    }

    public function down(): void
    {
    }
};
