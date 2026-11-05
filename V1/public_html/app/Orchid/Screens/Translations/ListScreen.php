<?php

namespace App\Orchid\Screens\Translations;

use App\Models\Translation;
use App\Models\Variable;
use App\Orchid\Layouts\TranslationsTable;
use Orchid\Screen\Screen;

class ListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Переводы';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Список переводов';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'translations' => Translation::orderBy('slug')->get()
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @return Layout[]
     */
    public function layout(): array
    {
        return [
            TranslationsTable::class
        ];
    }
}
