<?php

namespace App\Providers;

use App\Models\Variable;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Orchid\Attachment\Models\Attachment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $setting = Variable::whereSlug('settings')->with('attachment')->firstOrFail();
        View::composer('layouts.app', function (\Illuminate\View\View $view) use ($setting) {
            $view->with('settings', $setting);
        });

        View::composer(['layouts.app', 'index'], function (\Illuminate\View\View $view) use ($setting) {
            $view->with('offer', Attachment::whereId(
                    $setting->data['offer'][app()->getLocale()][0] ?? $setting->data['offer']['ru'][0]
                )->first()->url ?? '#');
        });

        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
