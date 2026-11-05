<?php

function translate($slug)
{
    return !empty(config('app.translations')[$slug]) ? config('app.translations')[$slug] : $slug;
}



function monthsName($number)
{
    $number--;
    $monthsRuName = [
        'января',
        'февраля',
        'марта',
        'апреля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря'
    ];

    $monthsKkName = [
        'қантардың',
        'ақпанның',
        'наурыздың',
        'сәуірдің',
        'мамырдың',
        'маусымның',
        'шілденың',
        'тамыздың',
        'қыркүйектің',
        'қазанның',
        'қарашаның',
        'желтоқсанның',
    ];

    $monthsKkName = [
        'қантар',
        'ақпан',
        'наурыз',
        'сәуір',
        'мамыр',
        'маусым',
        'шілде',
        'тамыз',
        'қыркүйек',
        'қазан',
        'қараша',
        'желтоқсан',
    ];
    $monthsEnName = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
    ];

    $mName = 'months'.ucfirst(app()->getLocale()).'Name';

    return empty(${$mName}[$number]) ? $monthsRuName[$number] : ${$mName}[$number];
}
