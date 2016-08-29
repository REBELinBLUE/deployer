const webpack = require('webpack');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const WebpackShellPlugin = require('webpack-shell-plugin');
const Formatter = require('manifest-revision-webpack-plugin/format');
const pkg = require('./package.json');

exports.dependencies = function () {
  return Object.keys(pkg.dependencies).filter((value) => (
    ['respond.js', 'html5shiv', 'font-awesome'].indexOf(value) === -1)
  );
};

exports.debug = function (isBuild, production) {
  if (isBuild) {
    return {
      debug: !production,
      devtool: production ? false : 'eval-source-map',
    };
  }

  return {
    debug: true,
    devtool: 'eval-source-map',
  };
};

exports.lint = function (path) {
  return {
    module: {
      preLoaders: [
        {
          test: /\.jsx?$/,
          loader: 'eslint',
          include: path,
        },
      ],
    },
  };
};

exports.devServer = function (options) {
  return {
    output: {
      publicPath: 'http://deployer.app:8080/build/',
    },
    watchOptions: {
      aggregateTimeout: 300,
      poll: 1000,
    },
    devServer: {
      historyApiFallback: false,
      hot: true,
      inline: true,
      quiet: true,
      stats: 'errors-only',
      host: options.host || '0.0.0.0',
      port: options.port || 8080,
      proxy: {
        '*': {
          target: 'http://deployer.app',
          changeOrigin: true,
          autoRewrite: true,
          xfwd: true,
        },
      },
    },
    plugins: [
      // Enable multi-pass compilation for enhanced performance in larger projects. Good default.
      new webpack.HotModuleReplacementPlugin({
        multiStep: true,
      }),
    ],
  };
};

exports.minify = function () {
  return {
    plugins: [
      new webpack.optimize.UglifyJsPlugin({
        compress: {
          warnings: false,
        },
      }),
    ],
  };
};

exports.setFreeVariable = function (key, value) {
  const env = {};
  env[key] = JSON.stringify(value);

  return {
    plugins: [
      new webpack.DefinePlugin(env),
    ],
  };
};

exports.clean = function (build, root) {
  return {
    plugins: [
      new CleanWebpackPlugin([build], {
        root,
      }),
      new WebpackShellPlugin({
        onBuildEnd: [`php ${root}/artisan js-localization:refresh --quiet`],
        onBuildExit: [`rm -f ${build}/css/*.js*`],
      }),
    ],
  };
};

exports.vendor = function (options) {
  const alias = {};
  alias[options.name] = options.path;

  return {
    module: {
      noParse: [options.path],
    },
    resolve: {
      alias,
    },
  };
};

exports.elixirFormatter = function (data, parsedAssets) {
  const format = new Formatter(data, parsedAssets);
  const outputData = format.general();

  // Webpack left over junk
  delete outputData.assets['css/app.js'];
  delete outputData.assets['css/vendor.js'];

  return JSON.stringify(outputData.assets, null, 2);
};
