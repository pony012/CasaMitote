var debug             = process.env.NODE_ENV !== 'production';
var webpack           = require('webpack');
var glob              = require('glob');
var path              = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

var scripts = {};

glob.sync('./resources/assets/js/*.js').forEach(function (file) {
    scripts[path.basename(file, path.extname(file))] = file;
});

module.exports = {
  context: __dirname,
  devtool: debug ? 'inline-sourcemap' : null,
  entry: scripts,
  module: {
    preLoaders: [
        {
            test: /\.jsx?$/,
            exclude: /(node_modules|bower_components)/,
            loader: 'source-map'
        }
    ],
    loaders: [
      {
        test: /\.jsx?$/,
        exclude: /(node_modules|bower_components)/,
        loader: 'babel-loader',
        query: {
            presets: ['react', 'es2015', 'stage-0'],
            plugins: [
              'react-html-attrs', 
              'transform-class-properties', 
              'transform-decorators-legacy', 
            ],
        }
      },
      {
        test: /\.(css|scss)$/,
        loader: ExtractTextPlugin.extract( 'style-loader', 'css-loader!postcss!sass-loader' )
      },
      {
        test: /\.(jpe?g|png|gif|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/i,
        loaders: [
            'url?limit=8192&name=../img/[name].[ext]',
            'img'
        ]
      },
      {
        test: /\.(otf|eot|ttf|woff|woff2)(\?v=[0-9]\.[0-9]\.[0-9])?$/i,
        loader: 'file-loader?name=../font/[name].[ext]'
      }
    ]
  },
  output: {
    filename: '[name].bundle.js',
    path: __dirname + '/public/js'
  },
  resolve: {
    modulesDirectories: ['node_modules', 'resources/assets/js'],
    alias: {
      $: 'jquery',
      jQuery: 'jquery',
      jquery: 'jquery'
    }
  },
  plugins: debug ? [
    new ExtractTextPlugin('../css/[name].bundle.css', { allChunks: true }),
    new webpack.ProvidePlugin({
        Promise: 'imports?this=>global!exports?global.Promise!es6-promise',
        fetch: 'imports?this=>global!exports?global.fetch!whatwg-fetch',
        React: 'react',
        ReactDOM: 'react-dom',
        Modernizr: 'modernizr',
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        _: 'lodash',
        Backbone: 'backbone'
    })
  ] : [
    new ExtractTextPlugin('../css/[name].bundle.css', { allChunks: true }),
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.OccurenceOrderPlugin(),
    new webpack.optimize.UglifyJsPlugin({ 
      mangle: true, 
      sourcemap: false, 
      minimize: true, 
      except: ['require', 'exports', '$'],
      compress: {warnings: false},
      output: {comments: false}
    }),
    new webpack.ProvidePlugin({
        Promise: 'imports?this=>global!exports?global.Promise!es6-promise',
        fetch: 'imports?this=>global!exports?global.fetch!whatwg-fetch',
        React: 'react',
        ReactDOM: 'react-dom',
        Modernizr: 'modernizr',
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        _: 'lodash',
        Backbone: 'backbone'
    })
  ],
  postcss: function () {
    return [require('autoprefixer')({ browsers: ['last 2 version'] }), require('precss')];
  }
};
