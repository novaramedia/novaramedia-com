/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import isNonEmptyString from './isNonEmptyString.js';

const UTM_KEYS = ['source', 'medium', 'campaign', 'content'];
const MAX_LENGTH = 255; // Mailchimp text merge field limit

/**
 * Hostname of the page that linked here, or an empty string.
 *
 * @returns {string} - Referrer hostname only, never the full URL
 */
function getReferrerHostname() {
  if (!document.referrer) {
    return '';
  }

  // An anchor parses any string without throwing, unlike new URL()
  const link = document.createElement('a');
  link.href = document.referrer;

  return link.hostname;
}

/**
 * Collects attribution context for a newsletter signup or donation form
 * submission: the current page path, the referring hostname and any utm_*
 * params on the current URL.
 *
 * Reads only the page the visitor is on right now. Nothing is read from or
 * written to storage, so this needs no cookie consent. Fields are prefixed
 * nm_ so they never pass as real utm_* params if forwarded to another site.
 *
 * @returns {Object} - Non-empty nm_* fields, ready for $.param()
 */
export default function getSignupAttribution() {
  const params = new URLSearchParams(window.location.search);
  const fields = {
    nm_page: window.location.pathname,
    nm_ref: getReferrerHostname(),
  };

  UTM_KEYS.forEach((key) => {
    fields[`nm_utm_${key}`] = params.get(`utm_${key}`) || '';
  });

  return Object.keys(fields).reduce((result, key) => {
    if (isNonEmptyString(fields[key])) {
      result[key] = fields[key].trim().slice(0, MAX_LENGTH);
    }

    return result;
  }, {});
}
