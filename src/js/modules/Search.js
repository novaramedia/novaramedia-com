/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */
import $ from 'jquery';

export class Search {
  onReady() {
    const _this = this;

     if ($('body').hasClass('error404')) {
      _this.fourzerofour();
    }
  }

  fourzerofour() {
    var href = window.location.href;
    // remove url before WP
    var request = href.split('/');
    // get last part of url

    // If trailing /
    if( request[request.length - 1] === '' ) {
      // Remove last element from the array
      request.pop();
    }

    request = request[request.length-1];
    // remove any dashes [in the case of a real permalink slug]
    request = request.replace(/-/g, ' ');

    if (request) {
      $('#search-input').val(request).focus();
    }
  }
}
