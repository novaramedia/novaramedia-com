/* jshint browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global $, jQuery, document, Site, Modernizr */

Site = {
  mobileThreshold: 601,
  init: function() {
    var _this = this;

    $(window).resize(function(){
      _this.onResize();
    });

    _this.fixWidows();

    _this.Header.init();
    _this.Search.init();
    _this.Support.init();
    _this.RadioPlayer.init();

    if ($('#single-resources-section').length) {
      _this.bindResourcesToggle();
    }

    $('.js-select').click(function() {
      $(this).selectText();
    });

  },

  onResize: function() {
    var _this = this;

  },

  fixWidows: function() {
    // utility class mainly for use on headines to avoid widows [single words on a new line]
    $('.js-fix-widows').each(function(){
      var string = $(this).html();
      string = string.replace(/ ([^ ]*)$/,'&nbsp;$1');
      $(this).html(string);
    });
  },

  bindResourcesToggle: function() {
    var _this = this;
    var $resources = $('#single-resources-section');

    $('#js-resources-toggle').click(function() {
      $resources.toggle();
    });

  },
};

Site.Header = {
  init: function() {
    var _this = this;

    _this.bind();

    if ($('body').hasClass('single')) {
      _this.showSinglePostTitle();
    }

  },

  bind: function() {
    var _this = this;

    $('#menu-toggle').click(function() {
      $('#header-sub').toggle();
    });

    $('#search-toggle').click(function() {
      $('#header-search').toggle();
    });

  },

  showSinglePostTitle: function(e) {
    var _this = this;

    _this.headerHeight = $('#header-main-wrapper').height();
    _this.$headerSinglePostTitle = $('#header-page-title');

    $(window).scroll(function() {

      if ($(window).scrollTop() > _this.headerHeight) {

        _this.$headerSinglePostTitle.css('opacity', 1);

      } else {

        _this.$headerSinglePostTitle.css('opacity', 0);

      }

    });

  }
};

Site.Search = {
  init: function() {
    var _this = this;

    _this.form = $('#search-input');
    _this.tags = $('.suggested-tag');
    _this.tagsList = $('#tag-suggestions');

    _this.bind();

     if ($('body').hasClass('error404')) {
      _this.fourzerofour();
    }

  },

  bind: function() {
    var _this = this;

    _this.form.on('change keyup input paste focus', function(event) {

      // Get Input Value
      var term = $(this).val();

      // Get matching tags
      var $suggested = _this.tags.siblings('.suggested-tag[data-tag^="' + term + '"]');

      // Hide all tags
      _this.tags.hide();

      // If matching tags, show them
      if( $suggested.length !== 0 ) {
        $suggested.show();
        _this.tagsList.show();
      } else {
        _this.tagsList.hide();
      }

    });
  },

  fourzerofour: function() {
    var href = window.location.href;
    // remove url before WP
    var request = href.split('/');
    // get last part of url

    // If trailing /
    if( request[request.length - 1] === "" ) {
      // Remove last element from the array
      request.pop();
    }

    request = request[request.length-1];
    // remove any dashes [in the case of a real permalink slug]
    request = request.replace(/-/g, ' ');

    if (request) {
      $('#search-input').val(request).focus();
    }
  },
};

Site.Support = {
  init: function() {
    if ($('.support-section').length) {
      this.bind();
    }
  },

  bind: function() {
    $('.support-form-slider').on('input', function() {
      var target = $(this).closest('.support-form').find('.support-form-value');
      target.html(this.value);
    });
  },
};

Site.RadioPlayer = {
  $radioPlayer: $('#radio-player'),
  playerIsInitialized: false,

  init: function() {
    var _this = this;

    _this.isBST = _this.checkBST();

    if ($('body').hasClass('home')) {

      if (_this.isSchedule()) {
        _this.goLive();
      }

      setInterval((function() {
        if (_this.isSchedule()) {
          _this.goLive();
        } else {
          _this.goOffline();
        }
      }), 15000);

    }

  },

  goLive: function() {
    var _this = this;

    if (!_this.playerIsInitialized) {
      _this.initPlayer();
    }

    _this.$radioPlayer.show();
  },

  goOffline: function() {
    var _this = this;

    if (_this.playerIsInitialized) {
      _this.$jPlayer.jPlayer('stop');
    }

    _this.$radioPlayer.hide();
  },

  initPlayer: function() {
    var _this = this;

    _this.$jPlayer = $('#jplayer').jPlayer({
      ready: function () {
        $(this).jPlayer('setMedia', {
          title: 'resonancefm',
          mp3: 'http://54.77.136.103:8000/resonance',
        });
      },
      cssSelectorAncestor: '#jp_container_1',
      swfPath: '/js',
      supplied: 'mp3',
      useStateClassSkin: true,
      autoBlur: false,
      smoothPlayBar: true,
      keyEnabled: true,
    }).bind($.jPlayer.event.play, function() {
      $('.jp-play').hide();
      $('.jp-stop').show();
    }).bind($.jPlayer.event.pause, function() {
      $('.jp-stop').hide();
      $('.jp-play').show();
    });

    _this.playerIsInitialized = true;
  },

  isSchedule: function() {
    var _this = this;

    var utc = new Date();
    var now;

    if (_this.isBST) {
      now = new Date(utc.getUTCFullYear(), utc.getUTCMonth(), utc.getUTCDate(), (utc.getUTCHours() + 1), utc.getUTCMinutes(), utc.getUTCSeconds());
    } else {
      now = new Date(utc.getUTCFullYear(), utc.getUTCMonth(), utc.getUTCDate(), utc.getUTCHours(), utc.getUTCMinutes(), utc.getUTCSeconds());
    }

    var day = now.getDay();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var dayminutes = (hours * 60) + minutes;

    if (day === 1 && dayminutes > 418 && dayminutes < 482) {
      return true;
    } else if (day === 5 && dayminutes > 778 && dayminutes < 842) {
      return true;
    }

    return false;

  },

  checkBST: function() {
    var d = new Date();

    for (var i = 31; i > 0; i--) {
      var tmp = new Date(d.getFullYear(), 2, i);
      if (tmp.getDay() === 0) {
        lSoM = tmp;
        break;
      }
    }

    for (var k = 31; k > 0; k--) {
      var tmpk = new Date(d.getFullYear(), 9, k);
      if (tmpk.getDay() === 0) {
        lSoO = tmpk;
        break;
      }
    }

    if (d < lSoM || d > lSoO) {
      return false;
    }

    return true;
  },

};

jQuery(document).ready(function () {
  'use strict';

  Site.init();

});

