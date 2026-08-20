/**
 * Live title hints for post_search_text fields.
 *
 * Re-resolves a field's hint when its value changes (typing or a pick in
 * the find-posts modal, which fires change). Markup contract mirrors
 * title_hint_html() in lib/meta/cmb2-post-search-field.php — keep the
 * classes and the ' · ' separator in sync with it. Display-only: any
 * failure leaves the form fully usable.
 */
jQuery(function ($) {
  'use strict';

  if (typeof nmPostResolve === 'undefined') {
    return;
  }

  var DEBOUNCE_MS = 300;

  function parseIds(value) {
    return String(value)
      .split(',')
      .map(function (part) { return parseInt(part, 10); })
      .filter(function (id) { return id > 0; });
  }

  function hintFor($input) {
    return $input.closest('.cmb-td').find('.nm-post-search-title').first();
  }

  function renderHint($hint, results) {
    var parts = results.map(function (info) {
      if (!info.found) {
        return $('<span>', { 'class': 'nm-post-search-title--broken', text: 'No post with ID ' + info.id });
      }
      if (info.status !== 'publish') {
        return $('<span>', {
          'class': 'nm-post-search-title--broken',
          text: info.title + ' — ' + info.status + ', won’t display publicly'
        });
      }
      return $('<span>', { text: info.title });
    });

    $hint.empty();
    parts.forEach(function ($part, i) {
      if (i > 0) {
        $hint.append(' · ');
      }
      $hint.append($part);
    });
  }

  function resolveInput($input) {
    var $hint = hintFor($input);

    if (!$hint.length) {
      return;
    }

    var ids = parseIds($input.val());

    if (!ids.length) {
      $hint.empty();
      return;
    }

    var separator = nmPostResolve.endpoint.indexOf('?') === -1 ? '?' : '&';

    fetch(nmPostResolve.endpoint + separator + 'ids=' + ids.join(','), {
      headers: { 'X-WP-Nonce': nmPostResolve.nonce },
      credentials: 'same-origin'
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('resolve failed: ' + response.status);
        }
        return response.json();
      })
      .then(function (results) {
        renderHint($hint, results);
      })
      .catch(function () {
        $hint.empty(); // Endpoint down: show nothing rather than stale/false info.
      });
  }

  var timers = {};
  var timerSeq = 0;

  $(document).on('change input', '.cmb-type-post-search-text input[type="text"]', function () {
    var input = this;

    if (!input.dataset.nmResolveTimer) {
      input.dataset.nmResolveTimer = String(++timerSeq);
    }

    var key = input.dataset.nmResolveTimer;
    clearTimeout(timers[key]);
    timers[key] = setTimeout(function () {
      resolveInput($(input));
    }, DEBOUNCE_MS);
  });
});
