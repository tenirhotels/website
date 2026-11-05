<?php

namespace App\Orchid;

use Laravel\Scout\Searchable;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemMenu;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * @return ItemMenu[]
     */
    public function registerMainMenu(): array
    {
        return [
            ItemMenu::label('Главная страница')
                ->icon('book-open')
                ->route('platform.home.form')
                ->title('Основное'),
            ItemMenu::label('Новости')
                ->icon('book-open')
                ->route('platform.posts.list'),
            ItemMenu::label('Активности')
                ->icon('compass')
                ->route('platform.activities.list'),
            ItemMenu::label('Наша философия')
                ->icon('building')
                ->route('platform.about.form'),
            ItemMenu::label('Отель')
                ->icon('home')
                ->route('platform.hotel.form'),
            ItemMenu::label('Отель: Привилегии')
                ->icon('home')
                ->route('platform.hotel_privileges.list'),

            ItemMenu::label('Отель: Инфраструктуры')
                ->icon('home')
                ->route('platform.infrastructures.list'),

            ItemMenu::label('Отель: Удобства')
                ->icon('home')
                ->route('platform.hotel-facilities.form'),

            ItemMenu::label('Отель: Дополнительные сервисы')
                ->icon('home')
                ->route('platform.hotel-services.form'),

            ItemMenu::label('Переводы')
                ->icon('docs')
                ->route('platform.translation.list'),

            ItemMenu::label('Настройки')
                ->icon('settings')
                ->route('platform.settings.form'),
        ];
    }

    /**
     * @return ItemMenu[]
     */
    public function registerProfileMenu(): array
    {
        return [
            ItemMenu::label('Profile')
                ->route('platform.profile')
                ->icon('user'),
        ];
    }

    /**
     * @return ItemMenu[]
     */
    public function registerSystemMenu(): array
    {
        return [
            ItemMenu::label(__('Access rights'))
                ->icon('lock')
                ->slug('Auth')
                ->active('platform.systems.*')
                ->permission('platform.systems.index')
                ->sort(1000),

            ItemMenu::label(__('Users'))
                ->place('Auth')
                ->icon('user')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->sort(1000)
                ->title(__('All registered users')),

            ItemMenu::label(__('Roles'))
                ->place('Auth')
                ->icon('lock')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->sort(1000)
                ->title(__('A Role defines a set of tasks a user assigned the role is allowed to perform.')),
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('Systems'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }

    /**
     * @return Searchable|string[]
     */
    public function registerSearchModels(): array
    {
        return [
            // ...Models
            // \App\Models\User::class
        ];
    }
}
