/* global require, __dirname, module */

const path = require('path');
const chalk = require('chalk');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

const CopyPlugin = require('copy-webpack-plugin');

const ImageMinimizerPlugin = require('image-minimizer-webpack-plugin');

const ESLintPlugin = require('eslint-webpack-plugin');
const BundleAnalyzerPlugin =
  require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const TerserPlugin = require('terser-webpack-plugin');
const postcssPresetEnv = require('postcss-preset-env');

var config = {
  entry: './src/js/main.js',

  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'main.js',
  },

  resolve: {
    extensions: ['.js', '.css', '.styl', '.svg'],
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            cacheDirectory: true,
            presets: [
              [
                '@babel/preset-env',
                {
                  useBuiltIns: 'entry',
                  corejs: 3.8,
                },
              ],
            ],
          },
        },
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
                plugins: [postcssPresetEnv(/* pluginOptions */)],
              },
            },
          },
          {
            loader: 'stylus-native-loader',
          },
        ],
      },
      {
        test: /\.(png|svg|jpg|jpeg|gif)$/i,
        type: 'asset/resource',
      },
    ],
  },

  plugins: [
    new ESLintPlugin(),
    new MiniCssExtractPlugin(),
    {
      apply: (compiler) => {
        compiler.hooks.compilation.tap('TimestampPlugin', () => {
          console.log(
            chalk.inverse.red(
              '————————————NO FUTURE———NEW COMPILATION NOW————————————'
            )
          );
          console.log(chalk.inverse.greenBright(new Date().toString()));
        });
      },
    },
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
    colors: true,
  },
};

module.exports = (env, argv) => {
  if (argv.mode === 'development') {
    config.devtool = 'source-map';
    config.watch = true;
  } else {
    config.optimization.minimizer.push(
      new ImageMinimizerPlugin({
        minimizer: {
          implementation: ImageMinimizerPlugin.imageminMinify,
          options: {
            plugins: [
              [
                'imagemin-gifsicle',
                { interlaced: false, optimizationLevel: 1 },
              ],
              ['imagemin-jpegtran', { progressive: false, arithmetic: false }],
              [
                'imagemin-optipng',
                {
                  optimizationLevel: 4,
                  bitDepthReduction: true,
                  colorTypeReduction: true,
                  paletteReduction: true,
                },
              ],
              [
                'imagemin-svgo',
                {
                  plugins: [
                    {
                      name: 'preset-default',
                      params: {
                        overrides: {
                          cleanupIds: false,
                          removeViewBox: false,
                        },
                      },
                    },
                  ],
                },
              ],
            ],
          },
        },
      })
    );
    config.plugins.push(
      new CopyPlugin({
        patterns: [
          {
            from: path.resolve(__dirname, 'src/img/'),
            to: path.resolve(__dirname, 'dist/img/'),
            globOptions: {
              ignore: ['**/*.DS_Store'],
            },
          },
          {
            from: path.resolve(__dirname, 'src/styl/fonts/'),
            to: path.resolve(__dirname, 'dist/fonts/'),
          },
        ],
      })
    );
    config.plugins.push(new BundleAnalyzerPlugin({ analyzerMode: 'static' }));
    config.performance.hints = 'warning';
    config.stats.preset = 'detailed';
  }

  return config;
};
