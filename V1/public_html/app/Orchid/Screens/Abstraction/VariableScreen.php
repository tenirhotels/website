<?php

namespace App\Orchid\Screens\Abstraction;

use App\Models\Variable;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

abstract class VariableScreen extends TranslationsScreen
{
    protected $variableKey = '';

    /**
     * Query data.
     *
     * @return array
     * @throws \Exception
     */
    public function query(): array
    {
        if (!$this->variableKey){
            throw new \Exception('variable key not defined');
        }

        return [
            'variable' => Variable::whereSlug($this->variableKey)->firstOrFail()->toArray()
        ];
    }

    public function save(Request $request)
    {
        $variable = Variable::whereSlug($this->variableKey)->firstOrFail();
        $variable->update($request->input('variable'));

        if ($request->has('variable.images')){
            $variable->attachment()->sync($request->input('variable.images', []));
        }

        if ($request->has('variable.files')) {
            $variable->attachment()->sync($request->input('variable.files', []));
        }

        Toast::success(__('Запрос успешно выполнен'));

        return redirect()->route('platform.' . $this->variableKey . '.form');
    }
}
