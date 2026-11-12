<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recognitions', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('award');   // award | press | rating
            $table->json('title');                          // «1 ключ MICHELIN»
            $table->json('subtitle')->nullable();           // «Tenir Eco Hotel Shymbulak, 2026»
            $table->string('source')->nullable();           // MICHELIN Guide, Tatler Asia…
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('show_on_home')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Подтверждённые по нескольким источникам (сентябрь 2026).
        $now = now();
        $rows = [
            [
                'kind' => 'award', 'sort' => 1, 'source' => 'MICHELIN Guide', 'year' => 2026,
                'url' => 'https://guide.michelin.com/en/hotels-stays/almaty/tenir-eco-hotel-shymbulak-mountain-resort-15864',
                'title' => ['ru' => '1 ключ MICHELIN', 'kk' => '1 MICHELIN кілті', 'en' => 'One MICHELIN Key', 'zh' => '米其林一钥'],
                'subtitle' => [
                    'ru' => 'Один из пяти отелей Казахстана, впервые вошедших в рейтинг MICHELIN Keys',
                    'kk' => 'MICHELIN Keys рейтингіне алғаш рет енген Қазақстанның бес қонақүйінің бірі',
                    'en' => 'One of the first five hotels in Kazakhstan in the MICHELIN Keys selection',
                    'zh' => '首次入选米其林钥匙榜单的哈萨克斯坦五家酒店之一',
                ],
            ],
            [
                'kind' => 'award', 'sort' => 2, 'source' => 'Tatler Asia', 'year' => 2026,
                'url' => 'https://www.tatlerasia.com/lifestyle/travel/tatler-best-kazakhstan-2026',
                'title' => ['ru' => 'Tatler Best-in-Class: Best Innovation', 'kk' => 'Tatler Best-in-Class: Best Innovation', 'en' => 'Tatler Best-in-Class: Best Innovation', 'zh' => 'Tatler Best-in-Class：最佳创新'],
                'subtitle' => [
                    'ru' => 'Tatler Best Kazakhstan 2026 — награда в категории отелей',
                    'kk' => 'Tatler Best Kazakhstan 2026 — қонақүйлер санатындағы марапат',
                    'en' => 'Tatler Best Kazakhstan 2026 — Hotels category',
                    'zh' => 'Tatler Best Kazakhstan 2026 酒店类奖项',
                ],
            ],
            [
                'kind' => 'press', 'sort' => 3, 'source' => 'Tatler Asia', 'year' => 2026,
                'url' => 'https://www.tatlerasia.com/lifestyle/travel/tatler-best-kazakhstan-2026',
                'title' => ['ru' => 'Tatler Best 10 Hotels', 'kk' => 'Tatler Best 10 Hotels', 'en' => 'Tatler Best 10 Hotels', 'zh' => 'Tatler Best 10 Hotels'],
                'subtitle' => [
                    'ru' => 'В первом списке лучших отелей Казахстана',
                    'kk' => 'Қазақстанның үздік қонақүйлерінің алғашқы тізімінде',
                    'en' => 'In the inaugural list of the best hotels in Kazakhstan',
                    'zh' => '入选哈萨克斯坦首届最佳酒店榜单',
                ],
            ],
        ];
        foreach ($rows as $row) {
            DB::table('recognitions')->insert([
                ...$row,
                'title' => json_encode($row['title'], JSON_UNESCAPED_UNICODE),
                'subtitle' => json_encode($row['subtitle'], JSON_UNESCAPED_UNICODE),
                'is_published' => true, 'show_on_home' => true, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recognitions');
    }
};
