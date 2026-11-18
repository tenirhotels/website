<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Впечатления: тип кнопки (бронирование / запрос в отдел продаж / звонок). Мероприятия — через отдел продаж. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('cta')->default('booking')->after('exely_service_id');
        });

        DB::table('experiences')->where('slug', 'events')->update(['cta' => 'sales']);

        $contacts = Setting::get('contacts', []) ?: [];
        $contacts['sales_phone'] = $contacts['sales_phone'] ?? '+7 700 427 18 48';
        $contacts['sales_email'] = $contacts['sales_email'] ?? 'sales@tenirhotels.com';
        Setting::put('contacts', $contacts);
    }

    public function down(): void
    {
        Schema::table('experiences', fn (Blueprint $table) => $table->dropColumn('cta'));
    }
};
