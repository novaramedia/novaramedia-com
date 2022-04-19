/* jshint esversion: 6, browser: true, devel: true, indent: 2, curly: true, eqeqeq: true, futurehostile: true, latedef: true, undef: true, unused: true */

import $ from 'jquery';
import moment from 'moment';
import Cookies from 'js-cookie';

import selectText from '../functions/selectText.js';

export class Utilities {
  constructor() {
    this.fixWidows();
    this.displayTimeSince();
    this.checkGDPRApproval();
  }

  bind() {
    const _this = this;

    if ($('#single-resources-section').length) {
      _this.bindResourcesToggle();
    }

    $('.js-select').click(function() {
      selectText($(this)[0]); // *** need to fix this
    });
  }

  bindResourcesToggle() {
    var $resources = $('#single-resources-section');

    $('#js-resources-toggle').click(function() {
      $resources.toggle();
    });
  }

  fixWidows() {
    // utility class mainly for use on headines to avoid widows [single words on a new line]
    $('.js-fix-widows').each(function(){
      var string = $(this).html();
      string = string.replace(/ ([^ ]*)$/,'&nbsp;$1');
      $(this).html(string);
    });
  }

  displayTimeSince() {
    $('.js-time-since').each(function() {
      var $element = $(this);
      var timestamp = $element.data('timestamp');
      var m = moment(timestamp);

      if (m.isAfter(moment().subtract(5, 'hours'))) {
        $element.text(`| ${m.fromNow()}`);
      }
    });
  }

  checkGDPRApproval() {
    var approvalCookie = Cookies.get('gdpr-approval');

    if (approvalCookie !== 'true') {
      $('#gdpr').show();

      $('#gdpr-accept').click(function() {
        Cookies.set('gdpr-approval', true);
        $('#gdpr').hide();
      });
    }
  }
}
