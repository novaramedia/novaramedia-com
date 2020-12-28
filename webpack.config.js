const path = require('path');
const webpack = require('webpack');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const CopyPlugin = require('copy-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const ESLintPlugin = require('eslint-webpack-plugin');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');

module.exports = {
  entry: './src/js/main.js',

  output: {
    path: path.resolve(__dirname, 'dist/js'),
    filename: 'main.js'
  },

  resolve: {
    extensions: ['.js', '.css', '.styl', '.svg']
  },

  mode: 'development',
  
  module: {
    rules: [{
      test: /\.js$/,
      exclude: /(node_modules|bower_components)/,
      use: {
        loader: 'babel-loader',
        options: {
          presets: ['@babel/preset-env']
        }
      }
    }, {
      test: /\.styl/,
      use: [
        {
          loader: MiniCssExtractPlugin.loader,
        },
        {
          loader: 'css-loader',
          options: {
            url: false,
          }
        }, {
          loader: 'stylus-loader'
        }
      ],
    }, {
      test: /\.(eot|woff|woff2|svg|ttf)([\?]?.*)$/,
      loader: 'file-loader',
    }],
  },

  plugins: [
    new ESLintPlugin(),
    new MiniCssExtractPlugin(),
    new MomentLocalesPlugin(),
    new WebpackNotifierPlugin({emoji: true, alwaysNotify: true}),
    // Copy the images folder and optimize all the images
    new CopyPlugin({
      patterns: [
        {
          from: path.resolve(__dirname, 'src/img/'),
          to: path.resolve(__dirname, 'dist/img/'),
        },
      ]
    }),
    new ImageminPlugin({
      test: /\.(jpe?g|png|gif|svg)$/i,
      gifsicle:{interlaced: false, optimizationLevel: 1},
      jpegtran:{progressive: false, arithmetic: false},
      optipng:{optimizationLevel: 4, bitDepthReduction: true, colorTypeReduction: true, paletteReduction: true},
      svgo:{plugins: [{cleanupIDs: false, removeViewBox: false}]},
    }),
  ],

  stats: {
    colors: true
  },

  devtool: 'source-map',
  watch: true,
};