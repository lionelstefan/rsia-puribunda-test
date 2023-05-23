const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .postCss('resources/css/app.css', 'public/css', [
//         //
//     ]);

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/main.scss', 'public/css/main.css')
    .sass('resources/sass/sidebar.scss', 'public/css/sidebar.css')
    .sass('resources/sass/login.scss', 'public/css/login.css');;