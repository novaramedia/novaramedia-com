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
              sourceMap: true,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
              postcssOptions: {
                plugins: [postcssPresetEnv(/* pluginOptions */)],
              },
            },
          },
          {
            loader: 'stylus-native-loader',
            options: {
              sourceMap: true,
              preferPathResolver: 'webpack',
            },
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
    new ESLintPlugin({
      configType: 'eslintrc',
      failOnError: false,
      failOnWarning: false,
      emitError: true,
      emitWarning: true,
      quiet: false,
      outputReport: false,
    }),
    new MiniCssExtractPlugin(),
    {
      apply: (compiler) => {
        // Timestamp plugin
        compiler.hooks.compilation.tap('TimestampPlugin', () => {
          console.log(
            chalk.inverse.red(
              '————————————NO FUTURE———NEW COMPILATION NOW————————————'
            )
          );
          console.log(chalk.inverse.greenBright(new Date().toString()));
        });

        // Enhanced error handling
        compiler.hooks.done.tap('ErrorHandlingPlugin', (stats) => {
          if (stats.hasErrors()) {
            console.log(chalk.red('\nCompilation failed with errors :{\n'));
            stats.compilation.errors.forEach((error) => {
              console.log(chalk.red('Error:'), error.message);
              if (error.module && error.module.resource) {
                console.log(chalk.yellow('File:'), error.module.resource);
              }
              if (error.details) {
                console.log(chalk.gray('Details:'), error.details);
              }
              console.log(''); // Empty line for separation
            });
          } else if (stats.hasWarnings()) {
            console.log(chalk.yellow('\nCompilation completed with warnings :\n'));
            stats.compilation.warnings.forEach((warning) => {
              console.log(chalk.yellow('Warning:'), warning.message);
            });
          } else {
            console.log(chalk.green('\nCompilation successful :]\n'));
          }
        });

        // Handle watch mode recompilation
        compiler.hooks.invalid.tap('WatchInvalidPlugin', (fileName) => {
          console.log(chalk.blue(`\nFile changed: ${fileName}`));
          console.log(chalk.blue('Recompiling...\n'));
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
    errors: true,
    errorDetails: true,
    warnings: true,
    moduleTrace: true,
    errorStack: true,
    chunks: false,
    modules: false,
    assets: false,
  },
};

module.exports = (env, argv) => {
  if (argv.mode === 'development') {
    config.devtool = 'source-map';
    config.watch = true;
    config.bail = false; // Don't stop on errors in development

    // Enhanced watch options
    config.watchOptions = {
      aggregateTimeout: 300,
      poll: false,
      ignored: /node_modules/,
    };

    // Development-specific stats
    config.stats = {
      preset: 'minimal',
      colors: true,
      errors: true,
      errorDetails: true,
      warnings: true,
      moduleTrace: true,
      timings: true,
      modules: false,
      chunks: false,
      assets: false,
    };

  } else {
    config.bail = true; // Stop on first error in production
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
