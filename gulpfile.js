const Elixir = require('laravel-elixir');
const gulp   = require('gulp');
const shell  = require('gulp-shell');
               require('laravel-elixir-remove');

Elixir.extend('lang', function () {
    new Elixir.Task('lang', function () {
        return gulp.src('')
                   .pipe(shell('php artisan js-localization:refresh --quiet'));
    });
});

const node_path = 'node_modules';

const paths = {
    'admin_lte'       : node_path + '/admin-lte',
    'ace'             : node_path + '/ace-min-noconflict',
    'backbone'        : node_path + '/backbone',
    'underscore'      : node_path + '/underscore',
    'moment'          : node_path + '/moment',
    'jquery'          : node_path + '/jquery',
    'jquery_sortable' : node_path + '/jquery-sortable',
    'fontawesome'     : node_path + '/font-awesome',
    'socketio_client' : node_path + '/socket.io-client',
    'ionicons'        : node_path + '/ionicons',
    'html5shiv'       : node_path + '/html5shiv',
    'respond'         : node_path + '/respond.js',
    'cropper'         : node_path + '/cropper',
    'toastr'          : node_path + '/toastr',
    'select2'         : node_path + '/admin-lte/plugins/select2',
    'localization'    : './vendor/andywer/js-localization'
};

Elixir(function(mix) {
    mix.lang()
    .styles([
        paths.admin_lte   + '/bootstrap/css/bootstrap.css',
        paths.select2     + '/select2.css',
        paths.fontawesome + '/css/font-awesome.css',
        paths.ionicons    + '/dist/css/ionicons.css',
        paths.admin_lte   + '/dist/css/AdminLTE.css',
        paths.admin_lte   + '/dist/css/skins/_all-skins.css',
        paths.toastr      + '/build/toastr.css',
        paths.cropper     + '/dist/cropper.css'
    ], 'public/css/vendor.css', './')
    .styles([
        'resources/assets/css/app.css',
        'resources/assets/css/console.css'
    ], 'public/css/app.css', './')
    .scripts([
        paths.html5shiv + '/dist/html5shiv.js',
        paths.respond   + '/dest/respond.src.js'
    ], 'public/js/ie.js', node_path)
    .scripts([
        paths.jquery          + '/dist/jquery.js',
        paths.jquery_sortable + '/source/js/jquery-sortable.js',
        paths.underscore      + '/underscore.js',
        paths.moment          + '/moment.js',
        paths.admin_lte       + '/bootstrap/js/bootstrap.js',
        paths.select2         + '/select2.js',
        paths.admin_lte       + '/dist/js/app.js',
        paths.backbone        + '/backbone.js',
        paths.socketio_client + '/socket.io.js',
        paths.localization    + '/resources/js/localization.js',
        paths.toastr          + '/toastr.js',
        paths.cropper         + '/dist/cropper.js',
        paths.ace             + '/ace.js',
        paths.ace             + '/mode-sh.js',
        paths.ace             + '/mode-php.js',
        paths.ace             + '/mode-yaml.js',
        paths.ace             + '/mode-ini.js'
    ], 'public/js/vendor.js', node_path)
    .babel([
        'app.jsx'
    ], 'public/js/app.js', 'resources/assets/js')
    // .scripts([
    //     'app.js',
    //     'projects.js',
    //     'templates.js',
    //     'servers.js',
    //     'heartbeats.js',
    //     'notifications.js',
    //     'notifyEmails.js',
    //     'shareFiles.js',
    //     'projectFiles.js',
    //     'checkUrls.js',
    //     'variables.js',
    //     'deployment.js',
    //     'commands.js',
    //     'users.js',
    //     'groups.js',
    //     'uploader.js',
    //     'profile.js'
    // ], 'public/js/app.js', 'resources/assets/js')
    // .copy(paths.admin_lte   + '/bootstrap/fonts/**', 'public/fonts')
    // .copy(paths.fontawesome + '/fonts/**',           'public/fonts')
    // .copy(paths.ionicons    + '/fonts/**',           'public/fonts')
    .copy([
        paths.admin_lte   + '/bootstrap/fonts/**',
        paths.fontawesome + '/fonts/**',
        paths.ionicons    + '/fonts/**'
    ], 'public/build/fonts')
    .version([
        'public/css/app.css',
        'public/css/vendor.css',
        'public/js/app.js',
        'public/js/ie.js',
        'public/js/vendor.js'
    ])
    .remove([
        'public/css',
        'public/js'
    ]);
});
