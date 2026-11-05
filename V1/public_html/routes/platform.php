<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Profile'), route('platform.profile'));
    });

// Platform > System > Users
Route::screen('users/{users}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(function (Trail $trail, $user) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('Edit'), route('platform.systems.users.edit', $user));
    });

// Platform > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.index')
            ->push(__('Users'), route('platform.systems.users'));
    });

// Platform > System > Roles > Role
Route::screen('roles/{roles}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(function (Trail $trail, $role) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Role'), route('platform.systems.roles.edit', $role));
    });

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Create'), route('platform.systems.roles.create'));
    });

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.index')
            ->push(__('Roles'), route('platform.systems.roles'));
    });


// posts...
Route::screen('posts/{post}/edit', \App\Orchid\Screens\Post\FormScreen::class)->name('platform.posts.edit');
Route::screen('posts/create', \App\Orchid\Screens\Post\FormScreen::class)->name('platform.posts.create');
Route::screen('posts', \App\Orchid\Screens\Post\ListScreen::class)->name('platform.posts.list');

// posts...
Route::screen('activities/{post}/edit', \App\Orchid\Screens\Activity\FormScreen::class)->name('platform.activities.edit');
Route::screen('activities/create', \App\Orchid\Screens\Activity\FormScreen::class)->name('platform.activities.create');
Route::screen('activities', \App\Orchid\Screens\Activity\ListScreen::class)->name('platform.activities.list');

// hotel privileges...
Route::screen('hotel-privileges/{hotelPrivilege}/edit', \App\Orchid\Screens\HotelPrivilege\FormScreen::class)->name('platform.hotel_privileges.edit');
Route::screen('hotel-privileges/create', \App\Orchid\Screens\HotelPrivilege\FormScreen::class)->name('platform.hotel_privileges.create');
Route::screen('hotel-privileges', \App\Orchid\Screens\HotelPrivilege\ListScreen::class)->name('platform.hotel_privileges.list');

// infrastructure
Route::screen('infrastructures/{infrastructure}/edit', \App\Orchid\Screens\Infrastructure\FormScreen::class)->name('platform.infrastructures.edit');
Route::screen('infrastructures/create', \App\Orchid\Screens\Infrastructure\FormScreen::class)->name('platform.infrastructures.create');
Route::screen('infrastructures', \App\Orchid\Screens\Infrastructure\ListScreen::class)->name('platform.infrastructures.list');


Route::screen('variable/about/edit', \App\Orchid\Screens\Variables\AboutScreen::class)
    ->name('platform.about.form');

Route::screen('variable/home/edit', \App\Orchid\Screens\Variables\HomeScreen::class)
    ->name('platform.home.form');

Route::screen('variable/hotel/edit', \App\Orchid\Screens\Variables\HotelScreen::class)
    ->name('platform.hotel.form');

Route::screen('variable/hotel-facilities/edit', \App\Orchid\Screens\Variables\HotelFacilityScreen::class)
    ->name('platform.hotel-facilities.form');

Route::screen('variable/hotel-services/edit', \App\Orchid\Screens\Variables\HotelServiceScreen::class)
    ->name('platform.hotel-services.form');

Route::screen('variable/settings/edit', \App\Orchid\Screens\Variables\SettingsScreen::class)
    ->name('platform.settings.form');


Route::screen('translation/form/{translation}', \App\Orchid\Screens\Translations\FormScreen::class)
    ->name('platform.translation.form');
Route::screen('translation', \App\Orchid\Screens\Translations\ListScreen::class)
    ->name('platform.translation.list');
