/**
 * ATF options preview — live behaviour.
 *
 * Paints the preview zones from the 8 picker fields' CURRENT values,
 * colours them by edit state (see atf-preview.css), detects collisions,
 * renders the primary-zone badges, and wires click-to-field. Display
 * strings share the contract of title_hint_html() (PHP) and
 * renderHint() (post-resolve.js) — keep the three in sync.
 * Display-only: failures show the banner and never block the form.
 */
jQuery(function ($) {
  'use strict';

  var $preview = $('[data-nm-atf-preview]');

  if (!$preview.length) {
    return;
  }

  var DEBOUNCE_MS = 300;
  var cache = {}; // id -> resolve result, shared across repaints
  var baseline = {}; // field id -> value at page load
  var serverState = {}; // field id -> snapshot of the PHP-rendered zone, for non-destructive fallback
  var timer = null;
  var paintSeq = 0;

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

  function fieldInput(fieldId) {
    return $(rowSelector(fieldId)).find('input[type="text"]').first();
  }

  function zoneValue($zone) {
    var raw = fieldInput($zone.attr('data-field')).val() || '';
    var id = parseInt(String(raw).trim(), 10);

    return id > 0 ? id : 0;
  }

  function collectZones() {
    return $preview.find('[data-nm-zone]').map(function () {
      var $zone = $(this);

      return { $zone: $zone, field: $zone.attr('data-field'), label: $zone.attr('data-label'), id: zoneValue($zone) };
    }).get();
  }

  // Shared display contract — see file docblock.
  function zoneText(entry) {
    var info = cache[entry.id];

    if (!info) {
      return { text: '', broken: false };
    }
    if (!info.found) {
      return { text: 'No post with ID ' + entry.id, broken: true };
    }
    if (info.status !== 'publish') {
      return { text: (info.title || '(no title)') + ' — ' + (info.status_label || info.status) + ', won’t display publicly', broken: true };
    }

    return { text: info.title || '(no title)', broken: false };
  }

  // Snapshot of what PHP already rendered, taken before the first repaint.
  // If a later resolve fails for an id that's still unchanged, paint()
  // restores this instead of blanking a zone that was rendering fine.
  function captureServerState() {
    collectZones().forEach(function (entry) {
      var $zone = entry.$zone;
      var $title = $zone.find('.nm-atf-preview__zone-title');
      var classes = ($zone.attr('class') || '').split(/\s+/).filter(function (cls) {
        return cls.indexOf('is-') === 0;
      }).join(' ');

      serverState[entry.field] = {
        text: $title.text(),
        classes: classes,
        thumb: $zone.find('.nm-atf-preview__thumb').css('background-image') || ''
      };
    });
  }

  function paint() {
    var zones = collectZones();
    var counts = {};
    var collisions = [];

    zones.forEach(function (entry) {
      if (entry.id) {
        counts[entry.id] = (counts[entry.id] || 0) + 1;
      }
    });

    zones.forEach(function (entry) {
      var $zone = entry.$zone;
      var $title = $zone.find('.nm-atf-preview__zone-title');
      var changed = String(baseline[entry.field]) !== String(entry.id || '');

      $zone.removeClass('is-empty is-broken is-changed is-collision');

      if (!entry.id) {
        $zone.addClass('is-empty');
        if (changed) {
          $zone.addClass('is-changed');
        }
        $title.text('Empty — click to set');
        setThumb($zone, null);
        return;
      }

      if (!cache[entry.id]) {
        // Resolve hasn't reported on this id (fetch failed, or nmPostResolve
        // is missing): don't blank a server-rendered zone. Unchanged ids
        // restore what PHP already rendered; ids picked this session (where
        // the resolve for the new value failed) get a neutral placeholder
        // rather than losing state or showing stale info.
        if (!changed) {
          var snapshot = serverState[entry.field];

          if (snapshot) {
            $title.text(snapshot.text);
            if (snapshot.classes) {
              $zone.addClass(snapshot.classes);
            }
            $zone.find('.nm-atf-preview__thumb').css('background-image', snapshot.thumb || '');
          }
        } else {
          $title.text('…');
          setThumb($zone, null);
        }
        return;
      }

      var display = zoneText(entry);
      var colliding = counts[entry.id] > 1;

      $title.text(display.text);
      setThumb($zone, cache[entry.id] && cache[entry.id].thumbnail);

      // Precedence red > amber > green: broken wins outright; collision
      // suppresses changed; changed only when valid and unique.
      if (display.broken) {
        $zone.addClass('is-broken');
      } else if (colliding) {
        $zone.addClass('is-collision');
      } else if (changed) {
        $zone.addClass('is-changed');
      }

      if (colliding && collisions.indexOf(entry.id) === -1) {
        collisions.push(entry.id);
      }
    });

    var $strip = $preview.find('[data-nm-collisions]');

    if (collisions.length) {
      var lines = collisions.map(function (id) {
        var names = zones.filter(function (entry) { return entry.id === id; })
          .map(function (entry) { return entry.label; });

        return 'Same post in ' + names.join(' and ');
      });

      $strip.text(lines.join('. ')).prop('hidden', false);
    } else {
      $strip.prop('hidden', true).empty();
    }
  }

  function setThumb($zone, url) {
    if ($zone.attr('data-thumb') !== '1') {
      return;
    }

    $zone.find('.nm-atf-preview__thumb').css('background-image', url ? 'url("' + url + '")' : '');
  }

  function resolveAndPaint() {
    var zones = collectZones();
    var missing = [];

    zones.forEach(function (entry) {
      if (entry.id && !cache[entry.id] && missing.indexOf(entry.id) === -1) {
        missing.push(entry.id);
      }
    });

    if (!missing.length || typeof nmPostResolve === 'undefined') {
      $preview.find('[data-nm-banner]').prop('hidden', true);
      paint();
      return;
    }

    var mySeq = ++paintSeq;
    var separator = nmPostResolve.endpoint.indexOf('?') === -1 ? '?' : '&';

    fetch(nmPostResolve.endpoint + separator + 'ids=' + missing.join(','), {
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
        results.forEach(function (info) {
          cache[info.id] = info;
        });
        // Always cache + repaint (paint() reads live input values, so it's
        // idempotent); only the banner is guarded, so a superseded response
        // can't flip it back to hidden after a newer request already failed.
        if (mySeq === paintSeq) {
          $preview.find('[data-nm-banner]').prop('hidden', true);
        }
        paint();
      })
      .catch(function () {
        if (mySeq === paintSeq) {
          $preview.find('[data-nm-banner]').prop('hidden', false);
        }
        paint(); // Paint what we know; unresolved ids get the non-destructive fallback (see paint()).
      });
  }

  function renderBadges() {
    $preview.find('[data-nm-badges]').each(function () {
      var $container = $(this);
      var fields = BADGE_FIELDS[$container.attr('data-block')] || [];

      $container.empty();

      // Production ignores more_on_section when the block is product-linked
      // (the front end short-circuits it), so suppress the badge here too
      // rather than showing a selection that won't actually render.
      var productLinkedField = fields.filter(function (badge) {
        return /is_product_linked$/.test(badge.field);
      })[0];
      var isProductLinked = !!productLinkedField && $(rowSelector(productLinkedField.field)).find('input[type="checkbox"]').is(':checked');

      fields.forEach(function (badge) {
        if (/more_on_section$/.test(badge.field) && isProductLinked) {
          return;
        }

        var $row = $(rowSelector(badge.field));

        if (!$row.length) {
          return;
        }

        if (badge.type === 'checkbox') {
          if ($row.find('input[type="checkbox"]').is(':checked')) {
            $container.append($('<span>', { 'class': 'nm-atf-preview__badge', text: badge.label }));
          }
          return;
        }

        var $option = $row.find('select option:selected');
        var value = $row.find('select').val();

        if (value && value !== 'none' && $option.length) {
          $container.append($('<span>', { 'class': 'nm-atf-preview__badge', text: badge.label + $option.text() }));
        }
      });
    });
  }

  // --- Wiring ---

  // Baseline: field values at page load, for the changed/unchanged grammar.
  collectZones().forEach(function (entry) {
    baseline[entry.field] = String(entry.id || '');
  });

  captureServerState();
  resolveAndPaint();
  renderBadges();

  // Zone fields: repaint (debounced) on typing or modal pick.
  $(document).on('change input', '.cmb-type-post-search-text input[type="text"]', function () {
    clearTimeout(timer);
    timer = setTimeout(resolveAndPaint, DEBOUNCE_MS);
  });

  // Badge fields: cheap, re-render immediately.
  $(document).on('change', '.cmb2-wrap input[type="checkbox"], .cmb2-wrap select', renderBadges);

  // Click (or Enter/Space) on a zone: scroll to and focus its field row.
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
