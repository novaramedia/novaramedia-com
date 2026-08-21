/**
 * Live title hints for post_search_text fields.
 *
 * Re-resolves a field's hint when its value changes (typing or a pick in
 * the find-posts modal, which fires change). Markup contract mirrors
 * title_hint_html() in lib/meta/cmb2-post-search-field.php — keep the
 * classes and the ' · ' separator in sync with it. Data comes from the
 * nm-post-resolve client (lib/admin/js/post-resolve.js). Display-only:
 * any failure leaves the form fully usable.
 */
jQuery(function ($) {
  'use strict';

  if (typeof nmPostResolveClient === 'undefined') {
    return;
  }

  var DEBOUNCE_MS = 300;

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
          text: info.title + ' — ' + (info.status_label || info.status) + ', won’t display publicly'
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

  var timers = {};
  var timerSeq = 0;

  function resolveInput($input, key) {
    // track() first: even if this value turns out empty, any in-flight
    // response for the field's previous value must not paint over it.
    var isCurrent = nmPostResolveClient.track('hint:' + key);

    var $hint = hintFor($input);

    if (!$hint.length) {
      return;
    }

    var ids = nmPostResolveClient.parseIds($input.val());

    if (!ids.length) {
      $hint.empty();
      return;
    }

    nmPostResolveClient.resolve(ids)
      .then(function (results) {
        if (!isCurrent()) { return; }
        renderHint($hint, results);
      })
      .catch(function () {
        if (!isCurrent()) { return; }
        $hint.empty(); // Endpoint down: show nothing rather than stale/false info.
      });
  }

  $(document).on('change input', '.cmb-type-post-search-text input[type="text"]', function () {
    var input = this;

    if (!input.dataset.nmResolveTimer) {
      input.dataset.nmResolveTimer = String(++timerSeq);
    }

    var key = input.dataset.nmResolveTimer;
    clearTimeout(timers[key]);
    timers[key] = setTimeout(function () {
      resolveInput($(input), key);
    }, DEBOUNCE_MS);
  });

  // CMB2 clones the previous group row on "Add Another Entry"; wipe the
  // cloned hint text and timer key so the new empty field doesn't show the
  // old row's title or share its debounce/sequence key.
  $(document).on('cmb2_add_row', function (evt, row) {
    $(row).find('.nm-post-search-title').empty();
    $(row).find('input[type="text"]').removeAttr('data-nm-resolve-timer');
  });
});
