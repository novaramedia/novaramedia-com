/**
 * Webpack configuration for admin-only JS (editor tooling).
 *
 * Uses @wordpress/scripts default configuration, like webpack.blocks.config.js,
 * so @wordpress/* imports are externalised to wp.* globals and each entry
 * emits an .asset.php carrying its dependency list and version hash.
 * Exists to avoid touching the theme's main webpack.config.js.
 */

const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    'meta-validation-classic': path.resolve(__dirname, 'src/admin/meta-validation/classic.js'),
    'meta-validation-block-editor': path.resolve(__dirname, 'src/admin/meta-validation/block-editor.js'),
  },
  output: {
    ...defaultConfig.output,
    path: path.resolve(__dirname, 'dist/admin'),
  },
  // The default config's CopyWebpackPlugin copies src/blocks block.json /
  // render.php into the output dir; that belongs to the blocks build only.
  plugins: defaultConfig.plugins.filter( ( plugin ) => plugin.constructor.name !== 'CopyPlugin' ),
};
