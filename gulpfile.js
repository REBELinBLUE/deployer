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

var bower_path = './vendor/bower_components';
var paths = {
    'ace'             : bower_path + '/ace-min-noconflict',
    'backbone'        : bower_path + '/backbone',
    'backbone_poller' : bower_path + '/backbone-poller',
    'underscore'      : bower_path + '/underscore',
    'moment'          : bower_path + '/moment',
    'jquery'          : bower_path + '/jquery',
    'jquery_sortable' : bower_path + '/jquery-sortable',
    'bootstrap'       : bower_path + '/bootstrap-sass-official/assets',
    'fontawesome'     : bower_path + '/fontawesome',
    'ionicons'        : bower_path + '/ionicons'
};

elixir(function(mix) {
    mix.sass('vendor.scss', 'public/css', {
        includePaths: [
            paths.bootstrap   + '/stylesheets',
            paths.fontawesome + '/scss',
            paths.ionicons    + '/scss'
        ]
    })
    .styles([
        'AdminLTE.css',
        'skin-green.css',
        'app.css'
    ], 'public/css/app.css', 'resources/assets/css')
    .scripts([
        paths.jquery          + '/dist/jquery.js',
        paths.jquery_sortable + '/source/js/jquery-sortable.js',
        paths.underscore      + '/underscore.js',
        paths.moment          + '/moment.js',
        paths.bootstrap       + '/javascripts/bootstrap.js',
        paths.backbone        + '/backbone.js',
        paths.backbone_poller + '/backbone.poller.js',
        paths.ace             + '/ace.js',
        paths.ace             + '/mode-sh.js',
        paths.ace             + '/mode-php.js',
        paths.ace             + '/mode-yaml.js',
        paths.ace             + '/mode-ini.js'
    ], 'public/js/vendor.js', bower_path)
    .scripts([
        'app.js',
        'projects.js',
        'servers.js',
        'heartbeats.js',
        'notifications.js',
        'notifyEmails.js',
        'shareFiles.js',
        'projectFiles.js',
        'deployment.js',
        'commands.js',
        'users.js',
        'groups.js',
        'AdminLTE.js'
    ], 'public/js/app.js', 'resources/assets/js')
    .copy(paths.bootstrap   + '/fonts/bootstrap/**', 'public/fonts')
    .copy(paths.fontawesome + '/fonts/**',           'public/fonts')
    .copy(paths.ionicons    + '/fonts/**',           'public/fonts')
    .version([
        'public/css/app.css',
        'public/css/vendor.css',
        'public/js/app.js',
        'public/js/vendor.js'
    ])
    .copy('public/fonts', 'public/build/fonts');
});
