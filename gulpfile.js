var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

var bower_path = "./vendor/bower_components";
var paths = {
    'jquery'        : bower_path + "/jquery",
    'bootstrap'     : bower_path + "/bootstrap-sass-official/assets",
    'fontawesome'   : bower_path + "/fontawesome",
    'ionicons'      : bower_path + "/ionicons"
};

elixir(function(mix) {
    mix.sass("style.scss", "public/css",
        {includePaths: [
            paths.bootstrap + '/stylesheets',
            paths.fontawesome + '/scss',
            paths.ionicons + '/scss'
        ]})
        .styles([
            "admin.min.css",
            "jvectormap.css",
            "skins.min.css"
        ], 'public/css/admin.css', 'resources/assets/css')

        .scripts([
            paths.jquery + '/dist/jquery.js',
            paths.bootstrap + '/javascripts/bootstrap.js',
        ], 'public/js/style.js', bower_path)
        .scripts([
            'admin.js',
            'app.min.js',
            'chart.js',
            'dashboard.js',
            'jvectormap.min.js',
            'jvectormap-world-mill-en.js',
            'sparkline.js'
        ], 'public/js/admin.js', 'resources/assets/js')

        .copy( paths.bootstrap + '/fonts/bootstrap/**', 'public/fonts')
        .copy( paths.fontawesome + '/fonts/**', 'public/fonts')
        .copy( paths.ionicons + '/fonts/**', 'public/fonts');
});
