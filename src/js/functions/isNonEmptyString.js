/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import { isString } from 'lodash';

/**
 * Checks if a value is a non-empty string (after trimming whitespace)
 * 
 * @param {*} val - The value to check
 * @returns {boolean} - True if the value is a string with non-whitespace content
 */
export default function isNonEmptyString(val) {
  return isString(val) && val.trim() !== '';
}
