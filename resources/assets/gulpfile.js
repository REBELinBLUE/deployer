const Elixir = require('laravel-elixir');
const join = require('path').join;
const del = require('del');

Elixir.extend('remove', (path) => {
    // const src = new Elixir.GulpPaths()
    //     .src(path);


    new Elixir.Task('remove', function() {
        this.recordStep('Removing Files');

        return del(path);
    }/*, src*/).watch(Elixir.config.assetsPath + '/**');
});

const node_path   = 'node_modules';
const public_path = join(__dirname, '../../public');
const source_path = join(__dirname, '../../');
const artisan_cmd = `php ${source_path}/artisan js-localization:refresh --quiet`;

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
    'localization'    : 'js'
};

Elixir(function(mix) {
    mix
    .exec(artisan_cmd)
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
        'app.css',
        'console.css'
    ], 'public/css/app.css', './css/')
    .scripts([
        paths.html5shiv + '/dist/html5shiv.js',
        paths.respond   + '/dest/respond.src.js'
    ], 'public/js/ie.js', './')
    .scripts([
        paths.localization    + '/localization.js',
        paths.jquery          + '/dist/jquery.js',
        paths.jquery_sortable + '/source/js/jquery-sortable.js',
        paths.underscore      + '/underscore.js',
        paths.moment          + '/moment.js',
        paths.admin_lte       + '/bootstrap/js/bootstrap.js',
        paths.select2         + '/select2.js',
        paths.admin_lte       + '/dist/js/app.js',
        paths.backbone        + '/backbone.js',
        paths.socketio_client + '/socket.io.js',
        paths.toastr          + '/toastr.js',
        paths.cropper         + '/dist/cropper.js',
        paths.ace             + '/ace.js',
        paths.ace             + '/mode-sh.js',
        paths.ace             + '/mode-php.js',
        paths.ace             + '/mode-yaml.js',
        paths.ace             + '/mode-ini.js'
    ], 'public/js/vendor.js', './')
    .webpack([
        'app.jsx'
    ], 'public/js/app.js', './js/')
    .copy([
        paths.admin_lte   + '/bootstrap/fonts/**',
        paths.fontawesome + '/fonts/**',
        paths.ionicons    + '/fonts/**'
    ], public_path + '/build/fonts')
    .version([
        'css/app.css',
        'css/vendor.css',
        'js/app.js',
        'js/ie.js',
        'js/vendor.js'
    ], public_path + '/build');
    //.remove('public');
});
