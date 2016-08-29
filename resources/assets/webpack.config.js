const join = require('path').join;
const merge = require('webpack-merge');
const HappyPack = require('happypack');
const validate = require('webpack-validator');
const ManifestPlugin = require('manifest-revision-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const DashboardPlugin = require('webpack-dashboard/plugin');
const webpack = require('webpack');
const tools = require('./parts');

const TARGET = process.env.npm_lifecycle_event;
const IS_PRODUCTION = (process.env.NODE_ENV === 'production');

process.env.BABEL_ENV = TARGET;

// Paths to the various folders
const PATHS = {
  app: join(__dirname, 'js'),
  styles: join(__dirname, 'css'),
  build: join(__dirname, '../../public/build'),
  node: join(__dirname, '/node_modules/'),
  root: join(__dirname, '../../'),
};

// The common config for all builds
let config = {
  entry: {
    'js/app': [PATHS.app],
    'js/vendor': tools.dependencies(),
    'js/ie': [ // FIXME: See if this is needed anymore, since react doesn't work prior to IE 9 anyway
      'html5shiv',
      join(PATHS.node, 'respond.js/dest/respond.src.js'),
    ],
    'css/app': `${PATHS.styles}/main.css`,
    'css/vendor': `${PATHS.styles}/vendor.css`,
  },
  resolve: {
    extensions: ['', '.js', '.jsx', '.css'],
    alias: {
      'jquery-ui': join(PATHS.node, 'jquery-ui/ui/widget.js'),
    },
    fallback: [PATHS.node],
  },
  externals: {
    lang: 'Lang',
  },
  output: {
    path: PATHS.build,
    publicPath: '/build/',
    filename: '[name].[hash].js',
    sourceMapFilename: '[file].map',
    chunkFilename: '[chunkhash].js',
  },
  module: {
    loaders: [
      {
        test: /\.(jpe?g|png|gif|svg)(\?\S*)?$/,
        loader: 'file',
        query: {
          name: 'images/[name].[hash].[ext]',
        },
        include: PATHS.node,
      },
      {
        test: /\.(woff2?|ttf|eot)(\?\S*)?$/,
        loader: 'file',
        query: {
          name: 'fonts/[name].[hash].[ext]',
        },
        include: PATHS.node,
      },
      {
        test: /\.jsx?$/,
        loaders: ['happypack/loader'],
        include: PATHS.app,
        exclude: PATHS.node,
      },
      {
        test: /\.css$/,
        loader: ExtractTextPlugin.extract('style', 'css?sourceMap'),
        include: PATHS.styles,
      },
    ],
  },
  plugins: [
    new HappyPack({
      loaders: ['react-hot', 'babel?cacheDirectory'],
      verbose: false,
      tempDir: join(PATHS.node, '.happypack-tmp'),
    }),
    new webpack.optimize.OccurenceOrderPlugin(),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
      'window.$': 'jquery',
    }),
    new ManifestPlugin(join(PATHS.build, 'rev-manifest.json'), {
      rootAssetPath: '/build/',
      extensionsRegex: /\.(css|js)$/i,
      format: tools.elixirFormatter,
    }),
    new ExtractTextPlugin('[name].[chunkhash].css'),
  ],
};

// Set up prebuilt assets - this is failing as HMR doesn't like it
// config = merge(common,
//   tools.vendor({
//     name: 'react',
//     path: join(PATHS.node, 'react/dist/react.js'),
//   }),
//   tools.vendor({
//     name: 'react-dom',
//     path: join(PATHS.node, 'react-dom/dist/react-dom.js'),
//   })
// );

if (TARGET === 'build') { // npm run build
  // Set up linting and clean up commands
  config = merge(config,
    tools.lint(PATHS.app),
    tools.clean(PATHS.build, PATHS.root)
  );
}

if (TARGET === 'build' || TARGET === 'stats') { // npm run build or npm run stats
  // Set up minfication and debugging
  config = merge(config,
    IS_PRODUCTION ? tools.minify() : {},
    tools.debug(true, IS_PRODUCTION),
    tools.setFreeVariable('process.env.NODE_ENV', JSON.stringify(process.env.NODE_ENV))
  );
} else { // npm start
  // Set up the dev server
  config.entry['js/app'].unshift('webpack/hot/only-dev-server');
  config.entry['js/app'].unshift('webpack-dev-server/client?http://deployer.app:8080/');

  config.plugins.unshift(new DashboardPlugin());

  config = merge(config,
    tools.debug(false),
    tools.devServer({
      host: process.env.HOST,
      port: process.env.PORT,
    })
  );
}

// Validate the config
module.exports = validate(config, {
  returnValidation: false,
  quiet: true,
});
