/* jshint browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global $, Site */

Site = {
  init: function() {
    var _this = this;

    _this.Utilities.init();
    _this.Header.init();
    _this.Search.init();
    _this.Support.init();
    _this.RadioPlayer.init();

    $(document).ready(function() {
      _this.Gallery.init();
    });
  }
};

Site.Utilities = {
  init: function() {
    var _this = this;

    _this.bind();
    _this.fixWidows();

  },

  bind: function() {
    var _this = this;

    if ($('#single-resources-section').length) {
      _this.bindResourcesToggle();
    }

    $('.js-select').click(function() {
      $(this).selectText();
    });

  },

  bindResourcesToggle: function() {
    var $resources = $('#single-resources-section');

    $('#js-resources-toggle').click(function() {
      $resources.toggle();
    });

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

    _this.$menuToggle = $('#menu-toggle');
    _this.$headerSub = $('#header-sub');
    _this.$searchToggle = $('#search-toggle');
    _this.$headerSearch = $('#header-search');
    _this.$searchInput = $('#search-input');

    _this.bind();

    if ($('body').hasClass('single')) {
      _this.showSinglePostTitle();
    }

  },

  bind: function() {
    var _this = this;

    _this.$menuToggle.click(function() {
      _this.$headerSub.toggle();
    });

    _this.$searchToggle.click(function() {
      _this.$headerSearch.toggle();
       _this.$searchInput.focus();
    });

  },

  showSinglePostTitle: function() {
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

Site.Gallery = {
  galleryInstances: [],

  init: function() {
    var _this = this;

    if ($('.swiper-container').length) {
      _this.initSwiperInstances();
    }
  },

  initSwiperInstances: function() {
    var _this = this;

    $('.swiper-container').each(function(index, item) {

      _this.galleryInstances[index] = new Swiper(item, {
        loop: true,
        pagination: '#gallery-pagination',
        paginationType: 'fraction',
        paginationFractionRender: function (swiper, currentClassName, totalClassName) {
          return '<span class="' + currentClassName + '"></span>/<span class="' + totalClassName + '"></span>';
        },
        onTap: function(swiper, event) {
          swiper.slideNext();
        },
      });
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

    _this.form.on('change keyup input paste', $.debounce( 500, function() {

      // Get Input Value
      var term = _this.slugify($(this).val());

      // Get matching tags
      var $suggested = _this.tags.siblings('[data-tag*="' + term + '"]');

      // Hide all tags
      _this.tags.hide();

      // If matching tags, show them
      if( $suggested.length !== 0 ) {
        $suggested.show();
        _this.tagsList.show();
      } else {
        _this.tagsList.hide();
      }
    }));
  },

  slugify: function(text) {
    return text.toString().toLowerCase()
      .replace(/\s+/g, '-')           // Replace spaces with -
      .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
      .replace(/\-\-+/g, '-')         // Replace multiple - with single -
      .replace(/^-+/, '')             // Trim - from start of text
      .replace(/-+$/, '');
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
    var _this = this;

    if ($('.support-section').length) {
      _this.bind();
    }

    _this.$progressBar = $('#progress-bar');

    if (_this.$progressBar) {
      _this.initProgressBar();
    }
  },

  bind: function() {
    $('.support-form-slider').on('input', function() {
      var target = $(this).closest('.support-form').find('.support-form-value');
      target.html(this.value);
    });
  },

  initProgressBar: function() {
    var _this = this;

    _this.$progressBar.css('width', '0%');

    $.get('https://payment.novaramedia.com/api/goal', null, function(data, textStatus) {

      if (textStatus === 'success') {
        var percent = data.percent;

        if (percent > 1) {
          percent = 1;
        }

        $('#progress-bar-row').slideDown(1250, function() {
          _this.$progressBar.css('width', (percent * 100) + '%');

          _this.displayProgressText(data);
        });
      }

    }, 'json');
  },

  displayProgressText: function(data) {
    var _this = this;

    var total = data.total / data.percent;
    var text = ': £' + _this.numberWithCommas(data.total) + ' of £' + _this.numberWithCommas(Math.round(total));

    $('#progress-text').text(text);
  },

  numberWithCommas: function(x) {
    // https://stackoverflow.com/questions/2901102/how-to-print-a-number-with-commas-as-thousands-separators-in-javascript#2901298
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  },
};

Site.RadioPlayer = {
  $radioPlayer: $('#radio-player'),
  playerIsInitialized: false,

  init: function() {
    var _this = this;

    // saves boolean for British Summer Time or not
    _this.isBST = _this.checkBST();

    if ($('body').hasClass('home')) {

      // checks if radio show is live in the schedule
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

    // checking if time is Monday between 7am and 8am with a little in and out flexibility
    // if not  checking if time is Friday between 1pm and 2pm with a little in and out flexibility
    if (day === 1 && dayminutes > 418 && dayminutes < 482) {
      return true;
    } else if (day === 5 && dayminutes > 778 && dayminutes < 842) {
      return true;
    }

    return false;

  },

  checkBST: function() {
    var d = new Date();
    var lSoM;
    var lSoO;

    // code from forgotton source. it checking against a pattern if date is in summer time or not
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

Site.init();
