/* jshint browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global $, jQuery, document, Site, Modernizr */

Site = {
  mobileThreshold: 601,
  init: function() {
    var _this = this;

    $(window).resize(function(){
      _this.onResize();
    });

    Site.search.init();

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

Site.search = {
  init: function() {
    var _this = this;

    _this.form = $('#search-input');

    _this.tags = $('.suggested-tag');

    _this.tagsList = $('#tag-suggestions');

    _this.bind();

  },

  bind: function() {
    var _this = this;

    _this.form.on('change keyup input paste', function(event) {

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
  }
};

jQuery(document).ready(function () {
  'use strict';

  Site.init();

});
