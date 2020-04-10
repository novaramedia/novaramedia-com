/* jshint browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
/* global $, Site, Cookies */

Site = {
  init: function() {
    var _this = this;

    _this.Utilities.init();
    _this.Header.init();
    _this.Search.init();
    _this.Support.init();

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
    _this.displayTimeSince();
    _this.checkGDPRApproval();
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

  displayTimeSince: function() {
    $('.js-time-since').each(function() {
      var $element = $(this);
      var timestamp = $element.data('timestamp');
      var m = moment(timestamp);

      if (m.isAfter(moment().subtract(5, 'hours'))) {
        $element.text('| ' + m.fromNow());
      }
    });
  },

  checkGDPRApproval: function() {
    var approvalCookie = Cookies.get('gdpr-approval');

    if (approvalCookie !== 'true') {
      $('#gdpr').show();

      $('#gdpr-accept').click(function() {
        Cookies.set('gdpr-approval', true);
        $('#gdpr').hide();
      });
    }
  }

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

     if ($('body').hasClass('error404')) {
      _this.fourzerofour();
    }

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

    if (_this.$progressBar.length) {
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

Site.init();