var elixir = require('laravel-elixir');
             require('laravel-elixir-remove');
             require('laravel-elixir-bower-io');
             require('laravel-elixir-vueify');

var gulp   = require('gulp');
var shell  = require('gulp-shell');

var Task = elixir.Task;
elixir.extend('lang', function() {
    new Task('lang', function(){
        return gulp.src('').pipe(shell('php artisan js-localization:refresh'));
    });
});

var bower_path = 'vendor/bower_components';

var paths = {
    'admin_lte'       : bower_path + '/admin-lte',
    'ace'             : bower_path + '/ace-min-noconflict',
    //'backbone'        : bower_path + '/backbone',
    'vue'             : bower_path + '/vue',
    'vue_resource'    : bower_path + '/vue-resource',
    'underscore'      : bower_path + '/underscore',
    'moment'          : bower_path + '/moment',
    'jquery'          : bower_path + '/jquery',
    'jquery_sortable' : bower_path + '/jquery-sortable',
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

elixir(function(mix) {
    mix.Bower()
    .styles([
        paths.admin_lte   + '/bootstrap/css/bootstrap.css',
        paths.select2     + '/select2.css',
        paths.fontawesome + '/css/font-awesome.css',
        paths.ionicons    + '/css/ionicons.css',
        paths.admin_lte   + '/dist/css/AdminLTE.css',
        paths.admin_lte   + '/dist/css/skins/_all-skins.css',
        paths.toastr      + '/toastr.css',
        paths.cropper     + '/dist/cropper.css',
    ], 'public/css/vendor.css', './')
    .styles([
        'resources/assets/css/app.css'
    ], 'public/css/app.css', './')
    .scripts([
        paths.html5shiv + '/dist/html5shiv.js',
        paths.respond   + '/dest/respond.src.js'
    ], 'public/js/ie.js', bower_path)
    .copy(paths.localization    + '/resources/js/localization.js', bower_path)
    .scripts([
        paths.jquery          + '/dist/jquery.js',
        paths.jquery_sortable + '/source/js/jquery-sortable.js',
        paths.underscore      + '/underscore.js',
        paths.moment          + '/moment.js',
        paths.admin_lte       + '/bootstrap/js/bootstrap.js',
        paths.select2         + '/select2.js',
        paths.admin_lte       + '/dist/js/app.js',
        paths.vue             + '/dist/vue.js',
        paths.vue_resource    + '/dist/vue-resource.js',
        //paths.backbone        + '/backbone.js',
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
    .browserify('app.js')
/*    .scripts([
        'app.js',
        'vue_servers.js',
        'projects.js',
        'templates.js',
        'servers.js',
        'heartbeats.js',
        'notifications.js',
        'notifyEmails.js',
        'shareFiles.js',
        'projectFiles.js',
        'checkUrls.js',
        'variables.js',
        'deployment.js',
        'commands.js',
        'users.js',
        'groups.js',
        'uploader.js',
        'profile.js'
    ], 'public/js/app.js', 'resources/assets/js') */
    .copy(paths.bootstrap   + '/fonts/bootstrap/**', 'public/fonts')
    .copy(paths.fontawesome + '/fonts/**',           'public/fonts')
    .copy(paths.ionicons    + '/fonts/**',           'public/fonts')
    .version([
        'public/css/app.css',
        'public/css/vendor.css',
        'public/js/app.js',
        'public/js/ie.js',
        'public/js/vendor.js'
    ])
    .copy('public/fonts', 'public/build/fonts')
    .remove([
        'public/css',
        'public/js',
        'public/fonts',
        bower_path + '/localization.js'
    ])
    .lang();
});
