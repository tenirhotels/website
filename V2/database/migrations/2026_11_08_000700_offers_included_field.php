<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Предложение: отдельное поле «Что входит в пакет». Раньше список лежал внутри описания
 * после заголовка (h2/h3) — режем описание по первому заголовку: до него — описание,
 * после (без самого заголовка) — «Что входит».
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('offers', 'included')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->json('included')->nullable()->after('description');
            });
        }

        foreach (DB::table('offers')->get() as $row) {
            $desc = json_decode($row->description ?? '', true);
            if (! is_array($desc)) {
                continue;
            }
            $included = [];
            foreach ($desc as $locale => $html) {
                if (! is_string($html)
                    || ! preg_match('~^(.*?)<h[23][^>]*>.*?</h[23]>(.*)$~is', $html, $m)) {
                    continue;
                }
                $desc[$locale] = trim($m[1]);
                $included[$locale] = trim($m[2]);
            }
            if ($included) {
                DB::table('offers')->where('id', $row->id)->update([
                    'description' => json_encode($desc, JSON_UNESCAPED_UNICODE),
                    'included' => json_encode($included, JSON_UNESCAPED_UNICODE),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('included');
        });
    }
};
