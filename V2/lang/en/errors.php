<?php

/** Страницы ошибок (404, 403, 419, 429, 500, 503). */
return [
    'altitude' => 'Altitude 3,200 m · error',
    'home' => 'Back to home',
    'rooms' => 'See the rooms',
    'retry' => 'Reload the page',
    'help' => 'Need help? We are here:',
    '404' => [
        'title' => 'No trail here',
        'text' => 'The page you are looking for has gone higher up the mountain, or never existed. Head back home and the way to the hotel will turn up.',
    ],
    '403' => [
        'title' => 'This door is closed',
        'text' => 'You do not have access to this page. If you think you should, write to us and we will sort it out.',
    ],
    '419' => [
        'title' => 'Session expired',
        'text' => 'The page was left open for too long. Reload it and try again.',
    ],
    '429' => [
        'title' => 'Too many requests',
        'text' => 'There is no rush at this altitude. Take a breath and try again in a minute.',
    ],
    '500' => [
        'title' => 'Something went wrong',
        'text' => 'An error occurred on our side. Reload the page in a minute. If it persists, call us and we will take your booking by phone.',
    ],
    '503' => [
        'title' => 'We will be right back',
        'text' => 'The site is under maintenance. Check back in a few minutes. You can still book by phone.',
    ],
];
