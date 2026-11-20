/**
 * ATF options preview — live behaviour.
 *
 * The grid itself is rendered by PHP (nm_atf_preview_render_grid() in
 * lib/admin/atf-preview.php). When a slot field changes, this sends the 8
 * current values to `GET nm/v1/atf-preview` and swaps in the returned grid,
 * since one slot can change the auto-fill and the Latest column everywhere.
 * On top of that it layers what only the browser knows: which slots were
 * edited this session (green), the same post in two slots (amber), and the
 * option badges under each main post. Click a post to jump to its field.
 * Display-only: failures show the banner and keep the last good grid.
 */
jQuery(function ($) {
  'use strict';

  var $preview = $('[data-nm-atf-preview]');

  if (!$preview.length || typeof nmAtfPreview === 'undefined') {
    return;
  }

  var DEBOUNCE_MS = 300;
  var FIELDS = [1, 2, 3, 4, 5, 6, 7, 8].map(function (n) { return 'nm_above_the_fold_featured_' + n; });
  var baseline = {}; // field id -> value at page load
  var timer = null;
  var requestSeq = 0;

  var BADGE_FIELDS = {
    1: [
      { field: 'nm_above_the_fold_featured_1_show_related', type: 'checkbox', label: 'See Also ✓' },
      { field: 'nm_above_the_fold_featured_1_more_on_section', type: 'select', label: 'More On: ' },
      { field: 'nm_above_the_fold_featured_1_is_product_linked', type: 'checkbox', label: 'Product-linked ✓' },
      { field: 'nm_above_the_fold_featured_1_has_embed', type: 'checkbox', label: 'Video embed ✓' }
    ],
    2: [
      { field: 'nm_above_the_fold_featured_5_show_related', type: 'checkbox', label: 'See Also ✓' },
      { field: 'nm_above_the_fold_featured_5_more_on_section', type: 'select', label: 'More On: ' },
      { field: 'nm_above_the_fold_featured_5_is_product_linked', type: 'checkbox', label: 'Product-linked ✓' }
    ]
  };

  function rowSelector(fieldId) {
    return '.cmb2-id-' + fieldId.replace(/_/g, '-');
  }

  function fieldValue(fieldId) {
    var id = parseInt(String($(rowSelector(fieldId)).find('input[type="text"]').first().val() || '').trim(), 10);

    return id > 0 ? id : 0;
  }

  // Session-only states: edited (green) and duplicate (amber). Broken (red),
  // auto-filled and empty come from the server-rendered grid.
  function markStates() {
    var counts = {};
    var values = {};

    FIELDS.forEach(function (field) {
      values[field] = fieldValue(field);
      if (values[field]) {
        counts[values[field]] = (counts[values[field]] || 0) + 1;
      }
    });

    var collisions = {};

    $preview.find('[data-nm-zone]').each(function () {
      var $zone = $(this);
      var field = $zone.attr('data-field');
      var value = values[field];

      $zone.removeClass('is-changed is-collision');

      if ($zone.hasClass('is-broken')) {
        return;
      }
      if (value && counts[value] > 1) {
        $zone.addClass('is-collision');
        (collisions[value] = collisions[value] || []).push($zone.attr('data-label'));
      } else if (String(baseline[field]) !== String(value)) {
        $zone.addClass('is-changed');
      }
    });

    var lines = Object.keys(collisions).map(function (id) {
      return 'Same post in ' + collisions[id].join(' and ');
    });
    var $strip = $preview.find('[data-nm-collisions]');

    if (lines.length) {
      $strip.text(lines.join('. ')).prop('hidden', false);
    } else {
      $strip.prop('hidden', true).empty();
    }
  }

  function renderBadges() {
    $preview.find('[data-nm-badges]').each(function () {
      var $container = $(this);
      var fields = BADGE_FIELDS[$container.attr('data-block')] || [];

      $container.empty();

      // The front end ignores More On when the block is product-linked, so
      // don't show a selection that won't render.
      var isProductLinked = fields.some(function (badge) {
        return /is_product_linked$/.test(badge.field) && $(rowSelector(badge.field)).find('input[type="checkbox"]').is(':checked');
      });

      fields.forEach(function (badge) {
        var $row = $(rowSelector(badge.field));

        if (!$row.length || (/more_on_section$/.test(badge.field) && isProductLinked)) {
          return;
        }

        if (badge.type === 'checkbox') {
          if ($row.find('input[type="checkbox"]').is(':checked')) {
            $container.append($('<span>', { 'class': 'nm-atf-preview__badge', text: badge.label }));
          }
          return;
        }

        var value = $row.find('select').val();
        var $option = $row.find('select option:selected');

        if (value && value !== 'none' && $option.length) {
          $container.append($('<span>', { 'class': 'nm-atf-preview__badge', text: badge.label + $option.text() }));
        }
      });
    });
  }

  function refresh() {
    var mine = ++requestSeq;
    var slots = FIELDS.map(function (field) { return fieldValue(field) || ''; }).join(',');
    var separator = nmAtfPreview.endpoint.indexOf('?') === -1 ? '?' : '&';

    fetch(nmAtfPreview.endpoint + separator + 'slots=' + encodeURIComponent(slots), {
      headers: { 'X-WP-Nonce': nmAtfPreview.nonce },
      credentials: 'same-origin'
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('preview failed: ' + response.status);
        }
        return response.json();
      })
      .then(function (data) {
        if (mine !== requestSeq) {
          return; // a newer request superseded this one
        }
        $preview.find('[data-nm-grid]').html(data.html);
        $preview.find('[data-nm-banner]').prop('hidden', true);
        renderBadges();
        markStates();
      })
      .catch(function () {
        if (mine === requestSeq) {
          $preview.find('[data-nm-banner]').prop('hidden', false);
          markStates();
        }
      });
  }

  // --- Wiring ---

  FIELDS.forEach(function (field) {
    baseline[field] = fieldValue(field);
  });

  renderBadges();
  markStates();

  $(document).on('change input', FIELDS.map(function (field) { return rowSelector(field) + ' input[type="text"]'; }).join(', '), function () {
    markStates();
    requestSeq++; // drop any in-flight response for the previous values
    clearTimeout(timer);
    timer = setTimeout(refresh, DEBOUNCE_MS);
  });

  $(document).on('change', '.cmb2-wrap input[type="checkbox"], .cmb2-wrap select', renderBadges);

  function goToField($zone) {
    var $row = $(rowSelector($zone.attr('data-field')));

    if (!$row.length) {
      return;
    }

    $row[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    $row.addClass('nm-atf-flash');
    setTimeout(function () {
      $row.removeClass('nm-atf-flash');
    }, 1500);
    $row.find('input[type="text"]').first().trigger('focus');
  }

  $preview.on('click', '[data-nm-zone]', function () {
    goToField($(this));
  });

  $preview.on('keydown', '[data-nm-zone]', function (evt) {
    if (evt.key === 'Enter' || evt.key === ' ') {
      evt.preventDefault();
      goToField($(this));
    }
  });
});
