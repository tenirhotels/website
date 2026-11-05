<?php

namespace App\Http\Middleware;

use App\Models\Translation;
use Closure;
use Illuminate\Support\Facades\Artisan;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //todo убрать
        if ($request->input('debug') == true){
            Artisan::call('view:clear');
            config([
                'app.env' => 'dev',
                'app.debug' => 'true'
            ]);
        }

        app()->setLocale(session('lang', config('app.locale')));

        config()->set('app.translations', Translation::all()->mapWithKeys(function ($item) {
            return [$item->slug => !empty($item->value) ? $item->value : $item->getTranslation('value', 'ru')];
        }));

        return $next($request);
    }
}
