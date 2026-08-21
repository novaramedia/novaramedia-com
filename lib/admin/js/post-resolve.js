/**
 * Post-resolve client.
 *
 * Thin browser client for `GET nm/v1/resolve-posts`. Registered as the
 * `nm-post-resolve` script handle by lib/admin/post-resolve.php, which also
 * attaches the nmPostResolve endpoint+nonce global this file reads.
 * UI-agnostic on purpose: consumers own their DOM and rendering, this file
 * owns the transport.
 *
 * API (window.nmPostResolveClient):
 *   parseIds(value) — comma list -> array of positive ints
 *   resolve(ids)    — array of ids -> Promise of the endpoint's result array
 *   track(key)      — stale-response guard: returns an isCurrent() function
 *                     that reports false once track(key) is called again
 */
window.nmPostResolveClient = (function () {
  'use strict';

  var seqs = {};

  function parseIds(value) {
    return String(value)
      .split(',')
      .map(function (part) { return parseInt(part, 10); })
      .filter(function (id) { return id > 0; });
  }

  function resolve(ids) {
    if (typeof nmPostResolve === 'undefined') {
      return Promise.reject(new Error('nmPostResolve data missing'));
    }

    var separator = nmPostResolve.endpoint.indexOf('?') === -1 ? '?' : '&';

    return fetch(nmPostResolve.endpoint + separator + 'ids=' + ids.join(','), {
      headers: { 'X-WP-Nonce': nmPostResolve.nonce },
      credentials: 'same-origin'
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('resolve failed: ' + response.status);
      }
      return response.json();
    });
  }

  function track(key) {
    var mine = (seqs[key] = (seqs[key] || 0) + 1);

    return function () {
      return seqs[key] === mine;
    };
  }

  return { parseIds: parseIds, resolve: resolve, track: track };
})();
