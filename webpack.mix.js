const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css');

mix.scripts([
  'resources/js/main.js',
  'resources/plugins/bootstrap-multiselect/index.js',
], 'public/js/main.js')

mix.styles([
  'resources/plugins/bootstrap-multiselect/index.css'
], 'public/css/main.css')
