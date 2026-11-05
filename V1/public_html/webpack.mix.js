const mix = require('laravel-mix');

mix.options({
    processCssUrls: false
}).version();

mix.copy('resources/js/app.js', 'public/js/app.js');

mix.styles([
    'resources/css/app.css'
], 'public/css/app.css');


mix.copyDirectory('resources/images', 'public/images');
mix.copyDirectory('resources/fonts', 'public/fonts');
