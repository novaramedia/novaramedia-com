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
 *                     (duplicates dropped; sent in batches of the endpoint's
 *                     20-id cap, results in input order)
 *   track(key)      — stale-response guard: returns an isCurrent() function
 *                     that reports false once track(key) is called again
 */
window.nmPostResolveClient = (function () {
  'use strict';

  var BATCH_SIZE = 20; // the endpoint's per-request cap (nm_resolve_posts_rest)

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

    var unique = ids.filter(function (id, i) { return ids.indexOf(id) === i; });
    var batches = [];

    for (var i = 0; i < unique.length; i += BATCH_SIZE) {
      batches.push(unique.slice(i, i + BATCH_SIZE));
    }

    return Promise.all(batches.map(resolveBatch)).then(function (results) {
      return [].concat.apply([], results);
    });
  }

  function resolveBatch(ids) {
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
