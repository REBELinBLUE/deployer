const Elixir = require('laravel-elixir');
const gulp   = require('gulp');
const shell  = require('gulp-shell');
const bower  = require('gulp-bower');
               require('laravel-elixir-remove');

// Elixir.extend('lang', function () {
//    new Elixir.Task('lang', function () {
//        return gulp.src('').pipe(shell('php artisan js-localization:refresh'));
//    });
// });

Elixir.extend('bower', function() {
    new Elixir.Task('bower', function() {
        return bower();
    });
});

const bower_path = 'bower_components';

const paths = {
    'admin_lte'       : bower_path + '/admin-lte',
    'ace'             : bower_path + '/ace-min-noconflict',
    'backbone'        : bower_path + '/backbone',
    'underscore'      : bower_path + '/underscore',
    'moment'          : bower_path + '/moment',
    'jquery'          : bower_path + '/jquery',
    'jquery_sortable' : bower_path + '/jquery-sortable',
    'jquery_complete' : bower_path + '/devbridge-autocomplete',
    'fontawesome'     : bower_path + '/fontawesome',
    'socketio_client' : bower_path + '/socket.io-client',
    'ionicons'        : bower_path + '/ionicons',
    'html5shiv'       : bower_path + '/html5shiv',
    'respond'         : bower_path + '/respond',
    'cropper'         : bower_path + '/cropper',
    'toastr'          : bower_path + '/toastr',
    'select2'         : bower_path + '/admin-lte/plugins/select2',
    'localization'    : 'vendor/andywer/js-localization'
};

Elixir(function(mix) {
    mix
    .bower()
    .styles([
        paths.admin_lte   + '/bootstrap/css/bootstrap.css',
        paths.select2     + '/select2.css',
        paths.fontawesome + '/css/font-awesome.css',
        paths.ionicons    + '/css/ionicons.css',
        paths.admin_lte   + '/dist/css/AdminLTE.css',
        paths.admin_lte   + '/dist/css/skins/_all-skins.css',
        paths.toastr      + '/toastr.css',
        paths.cropper     + '/dist/cropper.css'
    ], 'public/css/vendor.css', './')
    .styles([
        'resources/assets/css/app.css',
        'resources/assets/css/console.css'
    ], 'public/css/app.css', './')
    .scripts([
        paths.html5shiv + '/dist/html5shiv.js',
        paths.respond   + '/dest/respond.src.js'
    ], 'public/js/ie.js', bower_path)
    .copy(paths.localization    + '/resources/js/localization.js', bower_path)
    .scripts([
        paths.jquery          + '/dist/jquery.js',
        paths.jquery_sortable + '/source/js/jquery-sortable.js',
        paths.jquery_complete + '/dist/jquery.autocomplete.js',
        paths.underscore      + '/underscore.js',
        paths.moment          + '/moment.js',
        paths.admin_lte       + '/bootstrap/js/bootstrap.js',
        paths.select2         + '/select2.js',
        paths.admin_lte       + '/dist/js/app.js',
        paths.backbone        + '/backbone.js',
        paths.socketio_client + '/socket.io.js',
        bower_path            + '/localization.js',
        paths.toastr          + '/toastr.js',
        paths.cropper         + '/dist/cropper.js',
        paths.ace             + '/ace.js',
        paths.ace             + '/mode-sh.js',
        paths.ace             + '/mode-php.js',
        paths.ace             + '/mode-yaml.js',
        paths.ace             + '/mode-ini.js'
    ], 'public/js/vendor.js', bower_path)
    .scripts([
        'app.js',
        'projects.js',
        'templates.js',
        'servers.js',
        'heartbeats.js',
        'notifications.js',
        'shareFiles.js',
        'configFiles.js',
        'checkUrls.js',
        'variables.js',
        'deployment.js',
        'commands.js',
        'users.js',
        'groups.js',
        'uploader.js',
        'profile.js'
    ], 'public/js/app.js', 'resources/assets/js')
    .copy(paths.admin_lte   + '/bootstrap/fonts/**', 'public/fonts')
    .copy(paths.fontawesome + '/fonts/**',           'public/fonts')
    .copy(paths.ionicons    + '/fonts/**',           'public/fonts')
    .version([
        'public/css/app.css',
        'public/css/vendor.css',
        'public/js/app.js',
        'public/js/ie.js',
        'public/js/vendor.js'
    ])
    .copy('public/fonts', 'public/build/fonts');
    // .remove([
    //     'public/css',
    //     'public/js',
    //     'public/fonts'
    //     //bower_path + '/localization.js' // removing this breaks watch
    // ])
    //.lang();
});
