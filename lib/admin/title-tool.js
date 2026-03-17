(function ($) {
  'use strict';

  var TitleTool = {
    debounceTimer: null,
    debounceTime: 750,

    init: function () {
      this.bind();
    },

    bind: function () {
      var _this = this;

      $('#title').on('input', function () {
        clearTimeout(_this.debounceTimer);
        _this.debounceTimer = setTimeout(function () {
          _this.enforceTitleStyle();
        }, _this.debounceTime);
      });
    },

    enforceTitleStyle: function () {
      var raw = $('#title').val();
      $('#title').val(TitleCase.titleCase(raw));
    },
  };

  // Ported from: https://www.npmjs.com/package/ap-style-title-case
  var TitleCase = {
    stopwords: 'a an and at but by for in nor of on or so the to up yet'.split(' '),

    titleCase: function (str) {
      var _this = this;

      if (!str || !str.length) return null;

      var words = str.trim().split(/\s+/);

      return words
        .map(function (word, index) {
          if (index === 0) return _this.capitalize(word);
          if (index === words.length - 1) return _this.capitalize(word);
          if (_this.stopwords.indexOf(word.toLowerCase()) > -1) return word.toLowerCase();
          return _this.capitalize(word);
        })
        .join(' ');
    },

    capitalize: function (str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
  };

  $(window).on('load', function () {
    TitleTool.init();
  });
})(jQuery);
