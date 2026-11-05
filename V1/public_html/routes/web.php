<?php

use Illuminate\Support\Facades\Route;


Route::get('/lang/{lang}', 'LanguageController@update')->name('lang.update');

Route::get('/', 'HomeController@index')->name('home');
Route::get('/activities', 'ActivitiesController@index')->name('activities.index');
Route::get('/news', 'NewsController@index')->name('news.index');
Route::get('/news/{post}', 'NewsController@show')->name('news.show');
Route::get('/hotel', 'HotelController@index')->name('hotel.index');
Route::get('/about', 'AboutController@index')->name('about.index');
Route::get('/booking', 'BookingController@index')->name('booking.index');

/*Route::get('/lang/{lang}/booking', function () {
    return redirect('/booking');
});
*/
//Route::redirect('/lang/{lang}/booking', '/booking', 301);
