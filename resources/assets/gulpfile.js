const Elixir = require('laravel-elixir');
const join = require('path').join;
const removeFiles = require('gulp-remove-files');
const recipe = Elixir;

// Stop standard gulp output
// const gutil = require('gulp-util');
// gutil.log =  gutil.noop;

// Just because I am really anal and the mix.exec task is outputting some blank lines
// console.log = () => {
//   if (arguments.length && arguments[0].length) {
//     console.info.apply(this, arguments);
//   }
// };

// Elixir.config.assetsPath = __dirname;
// Elixir.config.publicPath = join(__dirname, '../../public');
// Elixir.config.appPath = join(__dirname, '../../app');
// Elixir.config.viewPath = join(__dirname, '../views');
// Elixir.config.versioning.buildFolder = 'build';

Elixir.extend('remove', (path) => {
  // const src = new Elixir.GulpPaths()
  //     .src(path);

  new Elixir.Task('remove', () => {
    //this.recordStep('Removing Files');

    return gulp.src([path])
      .pipe(removeFiles());
  }/* , src*/); //.watch(`${Elixir.config.assetsPath}/**`);
});

const nodePath = 'node_modules'; // This needs to be relative
const publicPath = join(__dirname, '../../public');
const sourcePath = join(__dirname, '../../');

const webpackConfig = {
  resolve: {
    extensions: ['.js', '.jsx'],
    fallback: [join(__dirname, '/node_modules/')],
  },
  externals: {
    jquery: 'jQuery',
  },
  module: {
    loaders: [
      {
        test: /.jsx?$/,
        loader: 'babel',
        exclude: /node_modules/,
        include: __dirname,
        query: {
          presets: ['es2015', 'react', 'stage-2'],
          comments: false,
        },
      },
    ],
  },
};

const paths = {
  admin_lte: `${nodePath}/admin-lte`,
  ace: `${nodePath}/ace-min-noconflict`,
  backbone: `${nodePath}/backbone`,
  underscore: `${nodePath}/underscore`,
  moment: `${nodePath}/moment`,
  jquery: `${nodePath}/jquery`,
  jquery_sortable: `${nodePath}/jquery-sortable`,
  fontawesome: `${nodePath}/font-awesome`,
  socketio_client: `${nodePath}/socket.io-client`,
  ionicons: `${nodePath}/ionicons`,
  html5shiv: `${nodePath}/html5shiv`,
  respond: `${nodePath}/respond.js`,
  cropper: `${nodePath}/cropper`,
  toastr: `${nodePath}/toastr`,
  select2: `${nodePath}/admin-lte/plugins/select2`,
  localization: 'js',
};

recipe((mix) => {
  // Update the language cache
  mix.exec(`php ${sourcePath}/artisan js-localization:refresh --quiet`);

  // Merge the CSS from the various vendor packages
  mix.styles([
    `${paths.admin_lte}/bootstrap/css/bootstrap.css`,
    // `${paths.select2}/select2.css`,
    `${paths.fontawesome}/css/font-awesome.css`,
    `${paths.ionicons}/dist/css/ionicons.css`,
    `${paths.admin_lte}/dist/css/AdminLTE.css`,
    `${paths.admin_lte}/dist/css/skins/_all-skins.css`,
    // `${paths.toastr}/build/toastr.css`,
    // `${paths.cropper}/dist/cropper.css`,
  ], 'public/css/vendor.css', './');

  // Merge the CSS from deployer
  mix.styles([
    'app.css',
    'console.css',
  ], 'public/css/app.css', './css/');

  // Merge the JS needed for IE
  mix.scripts([
    `${paths.html5shiv}/dist/html5shiv.js`,
    `${paths.respond}/dest/respond.src.js`,
  ], 'public/js/ie.js', './');

  // Merge the JS from the various vendor packages
  mix.scripts([
    `${paths.localization}/localization.js`,
    `${paths.jquery}/dist/jquery.js`,
    // `${paths.jquery_sortable}/source/js/jquery-sortable.js`,
    // `${paths.underscore}/underscore.js`,
    // `${paths.moment}/moment.js`,
    `${paths.admin_lte}/bootstrap/js/bootstrap.js`,
    // `${paths.select2}/select2.js`,
    `${paths.admin_lte}/dist/js/app.js`,
    // `${paths.backbone}/backbone.js`,
    // `${paths.socketio_client}/socket.io.js`,
    // `${paths.toastr}/toastr.js`,
    // `${paths.cropper}/dist/cropper.js`,
    // `${paths.ace}/ace.js`,
    // `${paths.ace}/mode-sh.js`,
    // `${paths.ace}/mode-php.js`,
    // `${paths.ace}/mode-yaml.js`,
    // `${paths.ace}/mode-ini.js`,
  ], 'public/js/vendor.js', './');

  // Run webpack on the deployer JS
  mix.webpack([
    'index.js',
  ], 'public/js/app.js', './js/', webpackConfig);

  // Copy the fonts needed by AdminLTE
  mix.copy([
    `${paths.admin_lte}/bootstrap/fonts/**`,
    `${paths.fontawesome}/fonts/**`,
    `${paths.ionicons}/fonts/**`,
  ], `${publicPath}/build/fonts`);

  // Version the files to enable cache busting
  mix.version([
    'css/app.css',
    'css/vendor.css',
    'js/app.js',
    'js/ie.js',
    'js/vendor.js',
  ], `${publicPath}/build`);

  // Browsersync
  // mix.browserSync({
  //   proxy: 'deployer.app'
  // });

  // Remove left over artifacts
  // mix.remove(`${__dirname}/public/**/*`);
});
