<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Единые названия канатных дорог, как на карте курорта: M-1 (Медеу — Шымбулак), K-1 (Шымбулак — Средняя),
 * K-2 (Средняя — Верхняя). Combi-1/Комби-1 → K-1, Combi-2/Комби-2 → K-2 в текстах из админки.
 * Названия станций — Медеу, Шымбулак, Средняя, Верхняя (см. lang/*\/map.php).
 */
return new class extends Migration
{
    private const TABLES = [
        'pages' => ['title', 'summary', 'content'],
        'faqs' => ['question', 'answer'],
        'hotels' => ['name', 'tagline', 'summary', 'description', 'highlights', 'getting_here'],
        'locations' => ['name', 'address', 'getting_here'],
        'offers' => ['title', 'summary', 'description', 'terms', 'included'],
        'experiences' => ['title', 'summary', 'description'],
        'events' => ['title', 'summary', 'description', 'place'],
        'room_types' => ['summary', 'description'],
        'stories' => ['title', 'summary', 'body'],
        'venues' => ['summary', 'description'],
    ];

    private const MAP = [
        'Combi-1' => 'K-1', 'Combi-2' => 'K-2', 'Комби-1' => 'K-1', 'Комби-2' => 'K-2',
        'Комби 1' => 'K-1', 'Комби 2' => 'K-2',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table => $columns) {
            if (! \Illuminate\Support\Facades\Schema::hasTable($table)) {
                continue;
            }
            $columns = array_values(array_filter($columns, fn ($c) => \Illuminate\Support\Facades\Schema::hasColumn($table, $c)));
            foreach (DB::table($table)->select(['id', ...$columns])->get() as $row) {
                $changes = [];
                foreach ($columns as $column) {
                    $raw = $row->{$column};
                    if (! is_string($raw) || $raw === '') {
                        continue;
                    }
                    // Переводимые поля хранятся как JSON {"ru": "...", "en": "..."} (кириллица в \uXXXX), поэтому меняем после разбора.
                    $decoded = json_decode($raw, true);
                    $new = is_array($decoded) ? json_encode($this->replace($decoded)) : $this->replace($raw);
                    if ($new !== $raw && (is_array($decoded) ? $new !== json_encode($decoded) : true)) {
                        $changes[$column] = $new;
                    }
                }
                if ($changes) {
                    DB::table($table)->where('id', $row->id)->update($changes);
                }
            }
        }
    }

    private function replace(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->replace($v), $value);
        }

        return is_string($value) ? strtr($value, self::MAP) : $value;
    }
};
