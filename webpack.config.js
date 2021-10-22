const path = require('path');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

const ImageminPlugin = require('imagemin-webpack-plugin').default;
const CopyPlugin = require('copy-webpack-plugin');

const ESLintPlugin = require('eslint-webpack-plugin');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const TerserPlugin = require('terser-webpack-plugin');

var config = {
  entry: './src/js/main.js',

  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'main.js'
  },

  resolve: {
    extensions: ['.js', '.css', '.styl', '.svg']
  },

  module: {
    rules: [{
      test: /\.js$/,
      exclude: /(node_modules|bower_components)/,
      use: {
        loader: 'babel-loader',
        options: {
          cacheDirectory: true,
          presets: [
            ['@babel/preset-env', {
              useBuiltIns: 'entry',
              corejs: 3.8
            }]
          ]
        }
      }
    },
    {
      test: /\.styl/,
      use: [
        {
          loader: MiniCssExtractPlugin.loader,
        },
        {
          loader: 'css-loader',
          options: {
            url: false,
          },
        },
        {
          loader: 'postcss-loader',
          options: {
            postcssOptions: {
              plugins: ['autoprefixer']
            }
          }
        },
        {
          loader: 'stylus-native-loader',
        },
      ],
    },
    ],
  },

  plugins: [
    new ESLintPlugin(),
    new MiniCssExtractPlugin(),
    new MomentLocalesPlugin(),
    // Copy the images folder and optimize all the images
    new CopyPlugin({
      patterns: [
        {
          from: path.resolve(__dirname, 'src/img/'),
          to: path.resolve(__dirname, 'dist/img/'),
          globOptions: {
            ignore: [
              "**/*.DS_Store",
            ],
          },
        },
        {
          from: path.resolve(__dirname, 'src/styl/fonts/'),
          to: path.resolve(__dirname, 'dist/fonts/'),
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

  optimization: {
    minimizer: [
      new TerserPlugin({
        terserOptions: {
          mangle: false,
          keep_fnames: true, // if we do decide to mangle still dont do this
        },
      }),
      new CssMinimizerPlugin(),
    ],
  },

  performance: {},

  stats: {
    preset: 'normal',
    colors: true
  },
};

module.exports = (env, argv) => {
  if (argv.mode === 'development') {
    config.devtool = 'source-map';
    config.watch = true;
  } else {
    config.plugins.push(new BundleAnalyzerPlugin());
    config.performance.hints = 'warning';
    config.stats.preset = 'detailed';
  }

  return config;
};
