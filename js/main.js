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
};

Site.Header = {
  init: function() {
    var _this = this;

    _this.bind();

  },

  bind: function() {

    $('#menu-toggle').click(function() {
      $('#header-sub').toggle();
    });

    $('#search-toggle').click(function() {
      $('#header-search').toggle();
    });

  },
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

jQuery(document).ready(function () {
  'use strict';

  Site.init();

});
