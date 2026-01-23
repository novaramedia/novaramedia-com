/**
 * Webpack configuration for Gutenberg blocks.
 *
 * Uses @wordpress/scripts default configuration.
 * This file exists to avoid conflicts with the theme's main webpack.config.js.
 */

const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
	...defaultConfig,
};
